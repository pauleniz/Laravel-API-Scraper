<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\API\V1\JobService;
use App\Jobs\API\V1\ScrapeJob;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Web Scraping API V1",
 *     description="This is a web scraping API built using Laravel."
 * ),
 * @OA\Server(
 *     url="http://localhost",
 *     description="Local development server"
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use your API token to authenticate",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum",
 * )
 */
class JobController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/jobs",
     *     summary="Create a scraping job",
     *     description="Creates a job with the provided URLs and CSS selectors for scraping.",
     *     tags={"Jobs"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="urls", type="array", @OA\Items(type="string", example="https://example.com")),
     *             @OA\Property(property="selectors", type="array", @OA\Items(type="string", example="h1"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="job_id", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $job = JobService::createJob($request);

        JobService::saveJob($job);

        ScrapeJob::dispatch($job);

        return response()->json(['job_id' => $job->getId()], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/jobs/{id}",
     *     summary="Get a specific job by ID",
     *     description="Fetches the details and status of a job using its ID.",
     *     tags={"Jobs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="job_id", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show($id)
    {
        $job = JobService::getJob($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($job->toArray());
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/jobs/{id}",
     *     summary="Delete a job by ID",
     *     description="Deletes a job and its associated data using its ID.",
     *     tags={"Jobs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Job deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($id)
    {
        $job = JobService::getJob($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], Response::HTTP_NOT_FOUND);
        }

        JobService::deleteJob($job);

        return response()->json(['message' => 'Job deleted'], Response::HTTP_OK);
    }
}
