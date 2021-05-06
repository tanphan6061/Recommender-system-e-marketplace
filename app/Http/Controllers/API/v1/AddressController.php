<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressR;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

    }

    public function index()
    {
        $user = Auth::user();
        $data = AddressR::collection($user->addresses->sortByDesc('active'));
        return $this->responded("Get list addresses", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param AddressCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressCreateRequest $request)
    {
        //
        $user = Auth::user();
        //dd($user);
        $isActive = $request->active ? 1 : 0;
        $validated = $request->validated();
        $ext_rules = [
            'active' => $isActive,
            //'user_id' => $user->id
        ];

        if ($isActive) {
            foreach ($user->addresses as $address) {
                $address->active = 0;
                $address->save();
            }
        }

        $user->addresses()->create(array_merge($validated, $ext_rules));
        $data = AddressR::collection($user->addresses->sortByDesc('active'));
        return $this->responded("Create address successfully", $data);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Address $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        //
        $user = Auth::user();
        if ($address->user->id == $user->id) {
            return $this->responded('Get detail address', new AddressR($address));
        }
        return $this->respondedError("Invalid");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Address $address
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Address $address
     * @return \Illuminate\Http\Response
     */
    public function update(AddressCreateRequest $request, Address $address)
    {
        //
        $user = Auth::user();
        if ($address->user->id == $user->id) {
            $isActive = $request->active ? 1 : 0;
            $validated = $request->validated();
            $ext_rules = [
                'active' => $isActive,
                //'user_id' => $user->id
            ];

            if ($isActive) {
                foreach ($user->addresses as $address) {
                    $address->active = 0;
                    $address->save();
                }
            }

            $address->update(array_merge($validated, $ext_rules));
            $data = AddressR::collection($user->addresses->sortByDesc('active'));
            return $this->responded("Update address successfully", $data);
        }

        return $this->respondedError("Invalid");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Address $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        $user = Auth::user();
        if ($address->user->id == $user->id) {
            $address->delete();
            $data = AddressR::collection($user->addresses->sortByDesc('active'));
            return $this->responded("Get list addresses", $data);
        }

        return $this->respondedError("Invalid");

    }
}
