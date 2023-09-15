<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Dto\TransactionData;
use App\Service\FileHandler;
use App\Storage\TransactionStorage;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TransactionStorageTest extends TestCase
{
    public function testSuccess(): void
    {
        $fileContentStub =  [
            "bin" => "4745030",
            "amount" => "2000.00",
            "currency" => "GBP"
        ];

        $fileHandlerMock = $this->createMock(FileHandler::class);

        $fileHandlerMock->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $fileHandlerMock->expects($this->once())
            ->method('readByLine')
            ->willReturnCallback(fn() => yield from [json_encode($fileContentStub)]);

        $transactionStorage = new TransactionStorage($fileHandlerMock, 'test');

        $expectedResult = [new TransactionData($fileContentStub['bin'], $fileContentStub['currency'], $fileContentStub['amount'])];

        $this->assertEquals($expectedResult, $transactionStorage->getDataByDsn('input.txt'));
    }

    public function testFileNotExists(): void
    {
        $fileHandlerMock = $this->createMock(FileHandler::class);

        $fileHandlerMock->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $fileHandlerMock->expects($this->never())
            ->method('readByLine');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Input file does not exist.');

        $transactionStorage = new TransactionStorage($fileHandlerMock, 'test');

        $transactionStorage->getDataByDsn('input.txt');
    }

    public function testMissingData(): void
    {
        $fileContentStub =  [
            "bin" => "4745030",
            "amount" => "2000.00",
        ];

        $fileHandlerMock = $this->createMock(FileHandler::class);

        $fileHandlerMock->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $fileHandlerMock->expects($this->once())
            ->method('readByLine')
            ->willReturnCallback(fn() => yield from [json_encode($fileContentStub)]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Wrong format data in the file.');

        $transactionStorage = new TransactionStorage($fileHandlerMock, 'test');

        $transactionStorage->getDataByDsn('input.txt');
    }
}