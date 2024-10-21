<?php

namespace App\Http\Services\API\V1;

use App\DTO\API\V1\JobDTO;
use App\Enums\API\V1\JobStatusEnum;
use App\Http\Controllers\Controller;
use App\Rules\ValidSelector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class JobService extends Controller
{
    public static function createJob(Request $request): JobDTO
    {
        $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'required|url',
            'selectors' => 'required|array',
            'selectors.*' => ['required', new ValidSelector()]
        ]);

        return new JobDTO(
            uniqid(),
            JobStatusEnum::PENDING,
            $request->input('urls'),
            $request->input('selectors')
        );
    }

    public static function saveJob(JobDTO $job): void
    {
        Redis::set(self::createKey($job->getId()), $job->toJson());
    }

    public static function getJob(string $id): JobDTO|null
    {
        $jobJson = Redis::get(self::createKey($id));

        if (!$jobJson) {
            return null;
        }

        return JobDTO::fromJson($jobJson);
    }

    public static function deleteJob(JobDTO $job): void
    {
        $jobKey = self::createKey($job->getId());

        Redis::del($jobKey);
    }

    private static function createKey(string $jobId): string
    {
        return 'job:'.$jobId;
    }

}
