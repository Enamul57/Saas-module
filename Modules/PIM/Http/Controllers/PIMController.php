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

class PIMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $employeeCreate;

    public function __construct(EmployeeCreate $employeeCreate)
    {
        $this->employeeCreate = $employeeCreate;
    }
    public function index()
    {
        return Inertia::render('PIM/PIM/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pim::create');
    }

    public function personal_details(Employee $employee)
    {
        $employee = $employee->load('personal_details.attachments');
        return Inertia::render('PIM/PIM/Personal_details', [
            'employee' => $employee
        ]);
    }
    public function contact_details(Employee $employee)
    {
        $employee = $employee->load('personal_details', 'contact_details');
        return Inertia::render('PIM/PIM/ContactDetails', [
            'employee' => $employee
        ]);
    }
    public function job_details(Employee $employee)
    {
        return Inertia::render('PIM/PIM/JobDetails', [
            'employee' => $employee
        ]);
    }
    public function salary_details(Employee $employee)
    {
        return Inertia::render('PIM/PIM/SalaryDetails', [
            'employee' => $employee
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeStore $request)
    {

        $employee = $this->employeeCreate->createEmployee($request->validated());
        return to_route('PIM.getPersonalDetails', $employee->id);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('pim::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('pim::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}
    /**
     * Create or Update Personal Details
     */
    public function storePersonalDetails(Request $request, Employee $employee)
    {
        //check if image exist or not of employee
        if ($request->hasFile('img')) {
            $employee->img ? deleteFile($employee->img) : null;
            $this->employeeCreate->updateEmployee($employee, $request->only(['first_name', 'middle_name', 'last_name', 'img']));
        }
        //employee validation && update
        if (!$request->hasFile('img')) {
            $this->employeeCreate->updateEmployee($employee, $request->only(['first_name', 'middle_name', 'last_name']));
        }
        //pim create or update with validation
        $pim_validation = $request->validate([
            'first_name'       => ['required', 'string', 'max:255'],
            'middle_name'      => ['nullable', 'string', 'max:255'],
            'last_name'        => ['required', 'string', 'max:255'],
            'dob'              => ['required', 'date', 'before:today'],
            'gender'           => ['required', Rule::in(['Male', 'Female'])],
            'marital_status'   => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'nationality'      => ['required', 'string', 'max:255'],
            'religion'         => ['nullable', 'string', 'max:255'],
            'blood_group'      => ['nullable', 'string', 'max:3'], // e.g., O+, AB-
            'national_id'     => [
                'required',
                'string',
                'max:20',
                Rule::unique('personal_details', 'national_id')->ignore($employee->id, 'employee_id')
            ],
            'passport_number'  => ['nullable', 'string', 'max:50'],
            'signature'        => ['nullable', 'image', 'max:2048'], // max 2MB
            'activity_log'     => ['nullable', 'string'],
        ]);
        $pim_validation['activity_log'] = (string)auth()->id();
        if ($request->hasFile('signature')) {
            $employee?->personal_details?->signature  ? deleteFile($employee?->personal_details->signature) : null;
            $pim_validation['signature'] = storeFile($request->file('signature'), 'signatures');
        } else {
            unset($pim_validation['signature']);
        }
        // ✅ Correct relationship usage
        $pim = $employee->personal_details()->updateOrCreate([], $pim_validation);
        //upload attachments
        if ($request->hasFile('attachments')) {
            $pim?->attachments  ? deleteFile($pim->attachments) : null;
            uploadAttachments($pim, $request->file('attachments'), 'employee_attachments');
        } else {
            unset($pim_validation['attachments']);
        }
        return to_route('pim.index');
    }
    //contact details
    public function storeContactDetails(ContactRequest $request, Employee $employee)
    {
        $validated = $request->validated();
        //update employee email
        $employee->email = $validated['work_email'];
        $employee->save();
        //store or update employee contact
        $validated['activity_log'] = auth()->id();
        $employee->contact_details()->updateOrCreate([], $validated);
        return to_route('PIM.ContactDetails', $employee);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
