<?php

namespace Modules\PIM\Http\Controllers;

use App\Http\Controllers\Controller;
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

class PIMController extends Controller
{
    protected $employeeCreate;

    public function __construct(EmployeeCreate $employeeCreate)
    {
        $this->employeeCreate = $employeeCreate;
    }

    public function index(Request $request)
    {
        // Fetch employees with their related data
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

    public function create()
    {
        return Inertia::render('PIM/PIM/Create');
    }

    public function store(EmployeeStore $request)
    {
        $validated = $request->validated();

        // ✅ Handle login credentials
        if ($request->has('showCredentials') && $request->showCredentials === true) {
            $validated['showCredentials'] = true;
            $validated['password'] = $request->password;
        } else {
            $validated['showCredentials'] = false;
        }

        // ✅ Handle linking existing user
        if ($request->has('link_user_id') && !empty($request->link_user_id)) {
            $validated['link_user_id'] = $request->link_user_id;
        }

        $employee = $this->employeeCreate->createEmployee($validated);

        return to_route('pim.getPersonalDetails', $employee->id)
            ->with('success', 'Employee created successfully!');
    }

    public function show($id)
    {
        return Inertia::render('PIM/PIM/Show', ['employee' => Employee::with(['personal_details', 'contact_details', 'jobDetails'])->findOrFail($id)]);
    }

    public function edit($id)
    {
        return Inertia::render('PIM/PIM/Edit', ['employee' => Employee::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        // Update logic
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            // Optional: Check if user has permission to delete
            // $this->authorize('delete', $employee);

            // Delete associated records first (if any)
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
            \Log::error('Error deleting employee: ' . $e->getMessage());
            return redirect()->route('pim.index')
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }
    public function personal_details(Employee $employee)
    {
        $employee = $employee->load('personal_details.attachments');
        return Inertia::render('PIM/PIM/Personal_details', [
            'employee' => $employee
        ]);
    }

    public function storePersonalDetails(Request $request, Employee $employee)
    {
        // ... your existing code ...
    }

    public function contact_details(Employee $employee)
    {
        $employee = $employee->load('personal_details', 'contact_details');
        return Inertia::render('PIM/PIM/ContactDetails', [
            'employee' => $employee
        ]);
    }

    public function storeContactDetails(ContactRequest $request, Employee $employee)
    {
        // ... your existing code ...
    }

    public function job_details(Employee $employee)
    {
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

    public function storeJobDetails(Request $request, Employee $employee)
    {
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

    public function salary_details(Employee $employee)
    {
        return Inertia::render('PIM/PIM/SalaryDetails', [
            'employee' => $employee
        ]);
    }

    public function employeeList(Request $request)
    {
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

    public function reports(Request $request)
    {
        // Basic statistics
        $stats = [
            'total_employees' => Employee::count(),
            // ✅ Fix: Use 'employee_status' instead of 'employment_status'
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
}
