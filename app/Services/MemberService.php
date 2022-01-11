<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Exceptions\ModelServiceException;
use App\Http\Requests\MemberCreateRequest;
use App\Http\Requests\MemberEditRequest;
use App\Models\Member;

class MemberService
{
    /**
     * @return array
     */
    public function paginate(): array
    {
        $request = request();

        $recordsTotal = Member::select(DB::raw('count(*) count'))->value('count');

        $members = Member::query()->select(
            'id',
            'first_name',
            'last_name',
            'email',
            'info',
            'image_path',
            'is_active',
            'created_at'
        )
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        $response = [];
        $response['draw'] = $request->input('draw', 1);
        $response['data'] = $members;
        $response['recordsTotal'] = $recordsTotal;
        $response['recordsFiltered'] = $recordsTotal;

        return $response;
    }

    /**
     * @param MemberCreateRequest $request
     * @return Member
     * @throws ModelServiceException
     */
    public function store(MemberCreateRequest $request): Member
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('member_image')) {
                $validatedData['image_path'] = $request->member_image->store('public/members');
                $validatedData['image_path'] = str_replace('public/members/', 'storage/members/', $validatedData['image_path']);
            }

            return Member::create($validatedData);
        } catch (\Exception | \Error $e) {
            throw new ModelServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param MemberEditRequest $request
     * @param Member $member
     * @return void
     * @throws ModelServiceException
     */
    public function update(MemberEditRequest $request, Member $member): void
    {
        try {
            $member->update($request->validated());
        } catch (\Exception | \Error $e) {
            throw new ModelServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
