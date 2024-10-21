<?php

namespace App\Jobs\API\V1;

use App\DTO\API\V1\JobDTO;
use App\Enums\API\V1\JobStatusEnum;
use App\Http\Services\API\V1\JobService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Panther\Client;
use Throwable;

class ScrapeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected JobDTO $jobDTO;

    public function __construct(JobDTO $jobDTO)
    {
        $this->jobDTO = $jobDTO;
    }

    public function handle(): void
    {
        $this->jobDTO->setStatus(JobStatusEnum::IN_PROGRESS);
        JobService::saveJob($this->jobDTO);

        $client = Client::createSeleniumClient('selenium:4444/wd/hub');
        $scrapedData = [];

        foreach ($this->jobDTO->getUrls() as $url) {
            $crawler = $client->request('GET', $url);

            foreach ($this->jobDTO->getSelectors() as $selector) {
                $scrapedData[$url][$selector][] = $crawler->filter($selector)->each(function ($node) {
                    return $node->text();
                });
            }
        }

        $this->jobDTO->setData($scrapedData);
        $this->jobDTO->setStatus(JobStatusEnum::COMPLETED);
        JobService::saveJob($this->jobDTO);
    }

    /**
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Job failed with error: ' . $exception->getMessage());

        $this->jobDTO->setStatus(JobStatusEnum::FAILED);
        JobService::saveJob($this->jobDTO);
    }
}
