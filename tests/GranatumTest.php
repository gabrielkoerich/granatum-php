<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GabrielKoerich\Granatum;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GabrielKoerich\Granatum\Customer;

class GranatumTest extends \PHPUnit\Framework\TestCase
{
    public function testSyncCustomers()
    {
        $seak = [
            'id' => 12345,
            'documento' => '555888999',
            'name' => 'random',
        ];

        $customers = Psr7\stream_for(json_encode([$seak]));

        // Create a mock and queue responses.
        $mock = new MockHandler([
            new Response(200, [], Psr7\stream_for(json_encode($seak))),
            new Response(200, [], $customers),
            new Response(200, [], $customers),
            new Response(200, [], Psr7\stream_for(json_encode($seak))),
            new Response(200, [], Psr7\stream_for(json_encode(['name' => 'new']))),
            new Response(200, []),
        ]);

        $handler = HandlerStack::create($mock);

        $customers = (new Customer)
            ->setHttpClient(new Client(['handler' => $handler]));

        $customer = $customers->create($seak);

        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals($customer->documento, '555888999');

        $customer = $customers->findByDocument('555888999');

        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals($customer->documento, '555888999');

        $this->assertTrue(is_null($customers->findByDocument('1234')));

        $find = $customers->find($customer->id);

        $this->assertEquals($find->id, $customer->id);

        $this->assertTrue(is_array($customer->toArray()));

        $this->assertEquals($customer->update(['name' => 'new'])->name, 'new');

        $customer->delete();
    }
}
