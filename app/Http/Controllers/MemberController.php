<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelServiceException;
use App\Http\Requests\MemberCreateRequest;
use App\Http\Requests\MemberEditRequest;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * @var MemberService
     */
    private $memberService;

    public function __construct()
    {
        $this->memberService = new MemberService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return array|View
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->memberService->paginate();
        }

        return view('members.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $data['member'] = new Member;

        return view('members.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MemberCreateRequest $request
     * @return Response
     * @throws ModelServiceException
     */
    public function store(MemberCreateRequest $request): Response
    {
        $id = $this->memberService->store($request);

        return response($id, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Member $member
     * @return Member|View
     */
    public function show(Request $request, Member $member)
    {
        if ($request->expectsJson()) {
            return $member;
        }

        $data['member'] = $member;

        return view('members.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Member $member
     * @return View
     */
    public function edit(Member $member): View
    {
        $data['member'] = $member;

        return view('members.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MemberEditRequest $request
     * @param Member $member
     * @return Response
     * @throws ModelServiceException
     */
    public function update(MemberEditRequest $request, Member $member): Response
    {
        $this->memberService->update($request, $member);

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Member $member
     * @return Response
     */
    public function destroy(Member $member): Response
    {
        $member->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function showTable(Request $request): string
    {
        $id = $request->input('id', 0);

        return "
            <table class='table table-striped'>
            <tr>
                <td>something: $id</td>
                <td>something</td>
                <td>something</td>
                <td>something</td>
            </tr>
            </table>
        ";
    }
}
