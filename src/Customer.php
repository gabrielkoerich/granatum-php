<?php

namespace Bulldesk\Granatum;

class Customer extends ApiResource
{
    /**
     * Find a customer by document.
     */
    public function findByDocument(string $document): ?self
    {
        $customers = $this->all();

        $customer = $customers->whereStrict('documento', $document)->first();

        if (is_null($customer)) {
            $customer = $customers->whereStrict('documento', $this->getNumbers($document))->first();
        }

        return $customer;
    }
}
