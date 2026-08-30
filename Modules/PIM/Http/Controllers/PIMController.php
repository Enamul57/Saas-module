<?php

namespace Modules\PIM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\traits\HasPermissionDirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Modules\PIM\app\Http\Requests\EmployeeStore;
use Modules\PIM\App\Models\Employee;
use Modules\PIM\app\services\EmployeeCreate;
use Modules\PIM\App\Http\Requests\PersonalDetailsRequest;
use Modules\PIM\App\Models\PersonalDetail;
use Modules\PIM\App\Http\Requests\ContactRequest;
use Modules\PIM\App\Models\JobCategory;
use Modules\PIM\App\Models\JobTitle;
use Modules\PIM\App\Models\JobUnit;
use Modules\PIM\App\Models\JobDetails;

class PIMController extends Controller
{
    use HasPermissionDirect;

    protected $employeeCreate;

    public function __construct(EmployeeCreate $employeeCreate)
    {
        $this->employeeCreate = $employeeCreate;
    }

    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_employees')) {
            abort(403, 'You do not have permission to view employees.');
        }

        $employees = Employee::with(['personal_details', 'contact_details', 'jobDetails'])
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('employee_id', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('PIM/PIM/Index', [
            'employees' => $employees,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('create_employee')) {
            abort(403, 'You do not have permission to create employees.');
        }

        return Inertia::render('PIM/PIM/Create');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(EmployeeStore $request)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('create_employee')) {
            abort(403, 'You do not have permission to create employees.');
        }

        $validated = $request->validated();

        // Handle login credentials
        if ($request->has('showCredentials') && $request->showCredentials === true) {
            $validated['showCredentials'] = true;
            $validated['password'] = $request->password;
        } else {
            $validated['showCredentials'] = false;
        }

        // Handle linking existing user
        if ($request->has('link_user_id') && !empty($request->link_user_id)) {
            $validated['link_user_id'] = $request->link_user_id;
        }

        $employee = $this->employeeCreate->createEmployee($validated);

        return to_route('pim.getPersonalDetails', $employee->id)
            ->with('success', 'Employee created successfully!');
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_employee_details')) {
            abort(403, 'You do not have permission to view employee details.');
        }

        return Inertia::render('PIM/PIM/Show', [
            'employee' => Employee::with(['personal_details', 'contact_details', 'jobDetails'])->findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('edit_employee')) {
            abort(403, 'You do not have permission to edit employees.');
        }

        return Inertia::render('PIM/PIM/Edit', [
            'employee' => Employee::findOrFail($id)
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, $id)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('edit_employee')) {
            abort(403, 'You do not have permission to edit employees.');
        }

        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('employees')->ignore($id)],
            'employee_id' => ['nullable', 'string', Rule::unique('employees')->ignore($id)],
            'img' => 'nullable|image|max:2048',
        ]);

        $employee->update($validated);

        return to_route('pim.index')->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('delete_employee')) {
            abort(403, 'You do not have permission to delete employees.');
        }

        try {
            $employee = Employee::findOrFail($id);

            // Delete associated records first
            if ($employee->personal_details) {
                $employee->personal_details->delete();
            }

            if ($employee->contact_details) {
                $employee->contact_details->delete();
            }

            if ($employee->jobDetails) {
                $employee->jobDetails->delete();
            }

            // Delete the employee
            $employee->delete();

            return redirect()->route('pim.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            return redirect()->route('pim.index')
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Display personal details form for an employee.
     */
    public function personal_details(Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_employee_details')) {
            abort(403, 'You do not have permission to view employee details.');
        }

        $employee = $employee->load('personal_details.attachments');
        return Inertia::render('PIM/PIM/Personal_details', [
            'employee' => $employee
        ]);
    }

    /**
     * Store personal details for an employee.
     */
    public function storePersonalDetails(PersonalDetailsRequest $request, Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('edit_employee')) {
            abort(403, 'You do not have permission to edit employee details.');
        }

        $validated = $request->validated();

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $paths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('employee_documents/' . $employee->id, 'public');
                $paths[] = $path;
            }
            $validated['attachments'] = $paths;
        }

        $personalDetails = $employee->personal_details()->updateOrCreate([], $validated);

        return to_route('pim.getPersonalDetails', $employee)
            ->with('success', 'Personal details saved successfully!');
    }

    /**
     * Display contact details form for an employee.
     */
    public function contact_details(Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_employee_details')) {
            abort(403, 'You do not have permission to view employee details.');
        }

        $employee = $employee->load('personal_details', 'contact_details');
        return Inertia::render('PIM/PIM/ContactDetails', [
            'employee' => $employee
        ]);
    }

    /**
     * Store contact details for an employee.
     */
    public function storeContactDetails(ContactRequest $request, Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('edit_employee')) {
            abort(403, 'You do not have permission to edit employee details.');
        }

        $validated = $request->validated();
        $contactDetails = $employee->contact_details()->updateOrCreate([], $validated);

        return to_route('pim.ContactDetails', $employee)
            ->with('success', 'Contact details saved successfully!');
    }

    /**
     * Display job details form for an employee.
     */
    public function job_details(Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_job_details')) {
            abort(403, 'You do not have permission to view job details.');
        }

        $employee->load(['jobDetails', 'jobDetails.jobCategory', 'jobDetails.jobTitle', 'jobDetails.jobUnit']);

        $jobCategories = JobCategory::all();
        $jobTitles = JobTitle::all();
        $jobUnits = JobUnit::all();

        $managers = Employee::where('id', '!=', $employee->id)
            ->select('id', 'first_name', 'last_name', 'email', 'img')
            ->get();

        return Inertia::render('PIM/PIM/JobDetails', [
            'employee' => $employee,
            'jobCategories' => $jobCategories,
            'jobTitles' => $jobTitles,
            'jobUnits' => $jobUnits,
            'managers' => $managers,
        ]);
    }

    /**
     * Store job details for an employee.
     */
    public function storeJobDetails(Request $request, Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('edit_job')) {
            abort(403, 'You do not have permission to edit job details.');
        }

        $validated = $request->validate([
            'job_title' => ['nullable', 'string', 'max:255'],
            'job_category' => ['nullable', 'string', 'max:255'],
            'job_unit' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employee_status' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'contract_start_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'job_category_id' => ['nullable', 'exists:job_categories,id'],
            'job_title_id' => ['nullable', 'exists:job_titles,id'],
            'job_unit_id' => ['nullable', 'exists:job_units,id'],
            'employment_status' => ['nullable', 'string', 'max:255'],
            'employee_type' => ['nullable', 'string', 'max:255'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'job_description' => ['nullable', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'reports_to' => ['nullable', 'exists:employees,id'],
            'confirmation_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
        ]);

        $validated['activity_log'] = auth()->id();

        $jobDetails = $employee->jobDetails()->updateOrCreate([], $validated);

        return to_route('pim.JobDetails', $employee)->with('success', 'Job details saved successfully!');
    }

    /**
     * Display salary details for an employee.
     */
    public function salary_details(Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_employee_salary')) {
            abort(403, 'You do not have permission to view employee salary.');
        }

        return Inertia::render('PIM/PIM/SalaryDetails', [
            'employee' => $employee
        ]);
    }

    /**
     * Store salary details for an employee.
     */
    public function storeSalaryDetails(Request $request, Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('edit_employee')) {
            abort(403, 'You do not have permission to edit employee salary.');
        }

        $validated = $request->validate([
            'salary' => ['nullable', 'numeric', 'min:0'],
            'salary_type' => ['nullable', 'string', 'max:255'],
            'pay_frequency' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:3'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'bank_routing' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'tax_filing_status' => ['nullable', 'string', 'max:255'],
            'allowances' => ['nullable', 'array'],
            'deductions' => ['nullable', 'array'],
        ]);

        $salaryDetails = $employee->salaryDetails()->updateOrCreate([], $validated);

        return to_route('pim.SalaryDetails', $employee)
            ->with('success', 'Salary details saved successfully!');
    }

    /**
     * Display employee list (alternative view).
     */
    public function employeeList(Request $request)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_employees')) {
            abort(403, 'You do not have permission to view employees.');
        }

        $employees = Employee::with([
            'personal_details',
            'contact_details',
            'jobDetails.jobCategory',
            'jobDetails.jobTitle',
            'jobDetails.jobUnit'
        ])
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('employee_id', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('PIM/PIM/EmployeeList', [
            'employees' => $employees,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Display HR reports.
     */
    public function reports(Request $request)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('view_reports')) {
            abort(403, 'You do not have permission to view reports.');
        }

        // Basic statistics
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::whereHas('jobDetails', function ($query) {
                $query->where('employee_status', 'active');
            })->count(),
            'total_departments' => JobCategory::count(),
            'new_hires' => Employee::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'departments' => JobCategory::withCount('jobDetails')->get(),
            'recent_hires' => Employee::with(['personal_details'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return Inertia::render('PIM/PIM/Reports', [
            'stats' => $stats
        ]);
    }

    /**
     * Export employees data.
     */
    public function export(Request $request)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('export_employees')) {
            abort(403, 'You do not have permission to export employees.');
        }

        // Export logic here
        // ...
    }

    /**
     * Import employees data.
     */
    public function import(Request $request)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('import_employees')) {
            abort(403, 'You do not have permission to import employees.');
        }

        // Import logic here
        // ...
    }

    /**
     * Manage employee documents.
     */
    public function documents(Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('manage_employee_documents')) {
            abort(403, 'You do not have permission to manage employee documents.');
        }

        return Inertia::render('PIM/PIM/Documents', [
            'employee' => $employee->load('documents')
        ]);
    }

    /**
     * Upload employee document.
     */
    public function uploadDocument(Request $request, Employee $employee)
    {
        // ✅ Check permission
        if (!$this->hasPermissionDirect('manage_employee_documents')) {
            abort(403, 'You do not have permission to manage employee documents.');
        }

        $validated = $request->validate([
            'document' => 'required|file|max:10240',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        // Upload logic here
        // ...

        return redirect()->back()->with('success', 'Document uploaded successfully!');
    }
}