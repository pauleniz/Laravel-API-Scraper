<?php

namespace App\DTO\API\V1;

use App\Enums\API\V1\JobStatusEnum;

class JobDTO
{
    private string $id;
    private JobStatusEnum $status;

    /**
     * @var String[] $urls
     */
    private array $urls;

    /**
     * @var String[] $selectors
     */
    private array $selectors;

    /**
     * @var String[] $data
     */
    private array $data;

    public function __construct(string $id, JobStatusEnum $status, array $urls = [], array $selectors = [], array $data = [])
    {
        $this->id = $id;
        $this->status = $status;
        $this->urls = $urls;
        $this->selectors = $selectors;
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): JobStatusEnum
    {
        return $this->status;
    }

    /**
     * @return String[]
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * @return String[]
     */
    public function getSelectors(): array
    {
        return $this->selectors;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function setStatus(JobStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'urls' => $this->urls,
            'selectors' => $this->selectors,
            'data' => $this->data,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return new self($data['id'], JobStatusEnum::from($data['status']), $data['urls'], $data['selectors'], $data['data']);
    }
}
