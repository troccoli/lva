<?php

namespace Tests\Concerns;

trait InteractsWithArrays
{
    private function assertArrayContent(array $expected, array $actual): void
    {
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual);
            $this->assertEquals($value, $actual[$key]);
        }
    }

    private function assertArrayContentByKey(string $key, $value, array $expected, array $actual): void
    {
        foreach ($actual as $item) {
            if (isset($item[$key]) && $item[$key] === $value) {
                $this->assertArrayContent($expected, $item);
                return;
            }
        }

        $message = sprintf('Cannot find data by \'%s\' => \'%s\' in array', $key, (string) $value);
        $this->fail($message);
    }
}
