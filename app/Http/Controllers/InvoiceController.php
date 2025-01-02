<?php

namespace App\Http\Controllers;

use App\Models\Account;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice()
    {
        FilamentInvoices::create()
            ->for(Account::find(1)) 
            ->from(Account::find(2)) 
            ->dueDate(now()->addDays(7)) 
            ->date(now()) 
            ->items([
                InvoiceItem::make('Item 1') 
                    ->description('Description 1')
                    ->qty(2)
                    ->price(100),
                InvoiceItem::make('Item 2') 
                    ->description('Description 2')
                    ->qty(1)
                    ->discount(10)
                    ->vat(10)
                    ->price(200),
            ])
            ->save(); 
        return response()->json(['message' => 'Invoice created successfully!']);
    }
}

