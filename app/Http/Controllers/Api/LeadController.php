<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LeadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Lead::query()
            ->status($request->query('status'))
            ->search($request->query('search'));
            if ($request->boolean('today')) {
                $query->whereDate('created_at', today());
            }
            $leads = $query->latest()->paginate(15);

            return LeadResource::collection($leads);
    }

    public function store(StoreLeadRequest $request): LeadResource
    {
        $lead = Lead::create([
            ...$request->validated(),
            'status' => 'new',
        ]);

        return new LeadResource($lead);
    }

    public function show(Lead $lead): LeadResource
    {
        return new LeadResource($lead);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): LeadResource
    {
        $lead->update($request->validated());

        return new LeadResource($lead->fresh());
    }

    public function destroy(Lead $lead): Response
    {
        $lead->delete();

        return response()->noContent();
    }
}
