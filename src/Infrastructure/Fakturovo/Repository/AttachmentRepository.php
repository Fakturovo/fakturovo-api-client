<?php

declare(strict_types=1);

namespace Fakturovo\ApiClient\Infrastructure\Fakturovo\Repository;

use Fakturovo\ApiClient\Domain\Repository\AbstractRepository;
use Fakturovo\ApiClient\Domain\Repository\AttachmentRepositoryInterface;
use Fakturovo\ApiClient\Domain\Response\DomainResponseInterface;
use Fakturovo\ApiClient\Domain\ValueObject\HttpMethod;
use Fakturovo\ApiClient\Infrastructure\Fakturovo\Exception\AttachmentException;
use GuzzleHttp\Psr7\MultipartStream;

final class AttachmentRepository extends AbstractRepository implements AttachmentRepositoryInterface
{
    public function uploadAttachment(string $contents, string $filename): DomainResponseInterface
    {
        $body = new MultipartStream([
            ['name' => 'file', 'contents' => $contents, 'filename' => $filename],
        ]);

        return $this->send(
            $this->apiRequestFactory->createRequest(
                HttpMethod::HTTP_METHOD_POST,
                '/v1/attachments',
                ['Content-Type' => 'multipart/form-data; boundary=' . $body->getBoundary()],
                $body,
            ),
            fn (\Throwable $e) => AttachmentException::failed("upload attachment [{$filename}]", $e),
        );
    }

    public function downloadAttachment(string $uuid): DomainResponseInterface
    {
        return $this->send(
            $this->jsonRequest(HttpMethod::HTTP_METHOD_GET, '/v1/attachments/' . rawurlencode($uuid)),
            fn (\Throwable $e) => AttachmentException::failed("download attachment [{$uuid}]", $e),
        );
    }
}
