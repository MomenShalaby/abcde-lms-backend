<?php

namespace App\Http\Controllers\Api\Experience;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\User;
use App\Traits\CanLoadRelationships;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
    use HttpResponses;
    use CanLoadRelationships;
    private $relations = ['user', 'hospital', 'country', 'state'];


    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $experiences = $this->loadRelationships($user->experiences());
        return $this->success([
            "experiences" => $experiences->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:2'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['sometimes', 'string', 'min:10'],
            'hospital_id' => ['required_without:otherHospital', 'prohibits:otherHospital', 'exists:hospitals,id'],
            'otherHospital' => ['required_without:hospital_id', 'prohibits:hospital_id'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['required', 'exists:states,id'],
        ]);
        $validated['user_id'] = Auth::id();
        $experience = Experience::create($validated);
        return $this->success([
            'experience' => $experience
        ], 'Experience added successfully', 201);

    }
    public function storeAll(Request $request)
    {
        $validated = $request->validate([
            'experiences' => ['required', 'array'],
            'experiences.*.title' => ['required', 'string', 'min:2'],
            'experiences.*.start_date' => ['required', 'date'],
            'experiences.*.end_date' => ['required', 'date', 'after:start_date'],
            'experiences.*.description' => ['sometimes', 'string', 'min:10'],
            'experiences.*.hospital_id' => ['required_without:experiences.*.otherHospital', 'prohibits:experiences.*.otherHospital', 'exists:hospitals,id'],
            'experiences.*.otherHospital' => ['required_without:experiences.*.hospital_id', 'prohibits:experiences.*.hospital_id'],
            'experiences.*.country_id' => ['required', 'exists:countries,id'],
            'experiences.*.state_id' => ['required', 'exists:states,id'],
        ]);
        foreach ($validated['experiences'] as $experience) {
            $experience['user_id'] = Auth::id();
            Experience::create($experience);
        }
        return $this->success('', 'Experiences added successfully', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Experience $experience)
    {
        $validated = $request->validate([
            'title' => ['required_without_all:start_date,end_date,description,hospital_id,otherHospital,country_id,state_id', 'string', 'min:2'],
            'start_date' => ['required_without_all:title,end_date,description,hospital_id,otherHospital,country_id,state_id', 'date'],
            'end_date' => ['required_without_all:start_date,title,description,hospital_id,otherHospital,country_id,state_id', 'date', 'after:start_date'],
            'description' => ['required_without_all:start_date,end_date,title,hospital_id,otherHospital,country_id,state_id', 'string', 'min:10'],
            'hospital_id' => ['required_without_all:start_date,end_date,description,title,otherHospital,country_id,state_id', 'prohibits:otherHospital', 'exists:hospitals,id'],
            'otherHospital' => ['required_without_all:start_date,end_date,description,hospital_id,title,country_id,state_id', 'prohibits:hospital_id'],
            'country_id' => ['required_without_all:start_date,end_date,description,hospital_id,otherHospital,title,state_id', 'exists:countries,id'],
            'state_id' => ['required_without_all:start_date,end_date,description,hospital_id,otherHospital,country_id,title', 'exists:states,id'],
        ]);
        $validated['user_id'] = Auth::id();
        $experience->update($validated);
        return $this->success([
            'experience' => $experience
        ], 'Experience updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience)
    {
        $experience->delete();
        return $this->success('', 'Experience Deleted Successfully');
    }
}
