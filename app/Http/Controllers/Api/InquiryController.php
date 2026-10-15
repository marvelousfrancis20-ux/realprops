<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inquiry\StoreInquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Models\Property;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function __construct(
        private readonly InquiryRepositoryInterface $repository,
        private readonly InquiryService $service,
    ) {}

    public function store(StoreInquiryRequest $request): JsonResponse
    {
        $inquiry = $this->service->createInquiry(
            $request->validated(),
            $request->user()
        );

        return (new InquiryResource($inquiry->load('property')))
            ->response()
            ->setStatusCode(201);
    }

    public function mine(Request $request)
    {
        return InquiryResource::collection(
            $request->user()
                ->sentInquiries()
                ->with('property.images')
                ->latest()
                ->paginate(15)
        );
    }

    public function scheduleTour(Request $request, int $propertyId): JsonResponse
    {
        $property = Property::query()
            ->whereKey($propertyId)
            ->where('is_approved', true)
            ->firstOrFail();

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $tour = $this->service->scheduleTour([
            'property_id' => $property->id,
            ...$data,
        ], $request->user());

        return response()->json(['message' => 'Tour request received.', 'tour' => $tour], 201);
    }
}
