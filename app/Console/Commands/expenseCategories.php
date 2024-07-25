<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class expenseCategories extends Command
{
    protected $signature = 'sortE';
    protected $description = 'Go through credit card transactions inside an excel file and assign categories based on the description';

    private $excelFilePath  = '';
    private $rules          = [];
    private $firstRow       = 3;
    private $categoryColumn = 5; //F
    private $creditColumn   = 2; //C
    private $debitColumn    = 3; //D
    private $currentRow     = 0;
    private $debug          = false;

    //contruct function
    function __construct()
    {
        parent::__construct();
        $this->excelFilePath = storage_path('app/2021YE.xlsx');
        $this->registerRules();
    }

    public function handle()
    {
        //open and prepare excel file for reading
        $reader = new Xlsx();
        //list all available tabs
        $spreadsheet = $reader->load($this->excelFilePath);
        //select tab name Credit Card
        $sheet = $spreadsheet->getSheetByName('Credit Card');
        //select records starting row 3
        $records = $sheet->rangeToArray('A'.$this->firstRow.':G'.$sheet->getHighestRow(), NULL, TRUE, FALSE);
        //dump first 3 records
        // $records = array_slice($records, 0, 50);
        // dump($records);
        //go through every records and assign category name to the category column for now only in array
        foreach($records as $key => $record)
        {
            $this->currentRow = $this->firstRow + $key;
            $category = $this->assignCategory($record);
            $records[$key][$this->categoryColumn] = $category;
            // $records[$key]['row'] = $this->currentRow;
        }
        //update category column in the excel file
        //store file copy of the file as result.xlxs
        $sheet->fromArray($records, NULL, 'A'.$this->firstRow);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(storage_path('app/result.xlsx'));
        
        // dump(collect($records)->map(function($record)
        // {
        //     return [
        //         'row'       => $record['row'],
        //         'description' => $record[1],
        //         'category'  => $record[$this->categoryColumn]
        //     ];
        // }));

    }

    private function assignCategory($record)
    {
        $category = '';
        $description = $record[1];
        $creditAmount = $record[$this->creditColumn];
        $debitAmount = $record[$this->debitColumn];
        $transactionType = $creditAmount > 0 ? 'credit' : 'debit';
        $amount = $transactionType === 'credit' ? abs($creditAmount) : abs($debitAmount);

        foreach($this->rules as $key => $catRules)
        {
            foreach($catRules['rules'] as $rulesSet)
            {

                // if ($this->currentRow == 90 && $rulesSet == $this->rules['OF']['rules'][2])
                // {
                //     $this->debug = true;
                //     dump($record);
                //     dump($rulesSet);
                //     // dump('contains',strpos($description, 'AMZN') !== false);
                // }
                // else
                // {
                //     $this->debug = false;
                // }
                $valid = true;
                foreach($rulesSet as $sigleRule)
                {
                    $valid = $this->validateRule($sigleRule, $description, $amount, $transactionType);
                    if(!$valid) break;
                }
                if($valid){
                    $category = $key;
                    break;
                }
            }
            if($category) break;
        }
       
        return $categoryName = $category ? $this->rules[$category]['name'] : null;
    }

    private function validateRule($signleRule, $description, $amount, $transactionType)
    {
        
        foreach($signleRule as $ruleType => $ruleStringValue)
        {
            
            switch($ruleType)
            {
                case 'transaction-type':
                    if ($this->debug)
                    {
                        dump('transaction-type', $transactionType, $ruleStringValue,$transactionType === $ruleStringValue);
                    }
                    if($transactionType !== $ruleStringValue) {
                        return false;
                    }
                    break;
                case 'end-with':
                    if(substr($description, -strlen($ruleStringValue)) !== $ruleStringValue) {
                        return false;
                    }
                    break;
                case 'starts-with':
                    if ($this->debug)
                    {
                        dump('starts-with', $description, $ruleStringValue,substr($description, 0, strlen($ruleStringValue)) === $ruleStringValue);
                    }
                    if(substr($description, 0, strlen($ruleStringValue)) !== $ruleStringValue) {
                        return false;
                    }
                    break;
                case 'contains':
                    if ($this->debug)
                    {
                        dump('contains', $description, $ruleStringValue,strpos($description, $ruleStringValue) !== false);
                    }
                    if(stripos($description, $ruleStringValue) === false) 
                    {
                        return false;
                    }
                    break;
                case 'min-amount':
                    if($amount < $ruleStringValue) {
                        return false;
                    }
                    break;
                case 'max-amount':
                    if($amount > $ruleStringValue) {
                        return false;
                    }
                    break;
            }
        }
        return true;
    }

    private function registerRules()
    {
        $this->rules = [
            'SU'    => [
                'name'      => 'Sub-contracts',
                'rules' => [
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => '*ZHE'],
                        ['contains' => 'PAYPAL']
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'Upwork']
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => '*MMRDOLLENTE'],
                    ],
                    //credit contains PAYPAL contains PAVELSHUTOV79
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => 'PAVELSHUTOV79'],
                    ]
                ],
            ],
            'ME'    => [
                'name'      => 'Meals and entertainement',
                'rules' => [
                ],
            ],
            'GI' => [
                'name' => 'Gifts',
                'rules' => [
                    
                ],
            ],
            'AD' => [
                'name' => 'Advertising',
                'rules' => [
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'DREAMSTIME.COM'],
                    ],
                    [
                        //credit
                        ['transaction-type' => 'credit'],
                        //contains
                        ['contains' => 'GOOGLE*'],
                        //contains
                        ['contains' => 'ADS'],
                    ],
                    //credit contains PAYPAL *KIJIJI CA
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => '*KIJIJI'],
                    ],
                ],
            ],
            'HO' => [
                'name' => 'Hosting, Storage & Domains',
                'rules' => [
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'GODADDY'],
                    ],
                    //credit contains PAYAPL contains NAMECHEAP
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => 'NAMECHEAP'],
                    ],
                    //credit contains LINODE.COM
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'LINODE.COM'],
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'DIGITAL'],
                        ['contains' => 'OCEAN'],
                        ['contains' => 'CANADA'],
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'AMZ*SENDA'],
                    ],
                ],
            ],
            'SB' => [
                'name' => 'Subscription Services',
                'rules' => [
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'Amazon.ca'],
                        ['contains' => 'Prime'],
                        ['contains' => 'Member'],
                    ],
                    //credit  contains PAYPAL CONTAINS *GOOGLE GOOGLE
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => '*GOOGLE'],
                    ],
                    [
                        //credit contains GOOGLE contains YouTubePremium
                        ['transaction-type' => 'credit'],
                        ['contains' => 'GOOGLE'],
                        ['contains' => 'YouTubePremium'],
                    ],
                    //contains GSUITE
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'GSUITE'],
                    ],
                ],
            ],
            'AF' => [
                'name' => 'Association Fees',
                'rules' => [
                    
                ],
            ],
            'CM' => [
                'name' => 'Internet/Phone',
                'rules' => [
                    [
                        //credit contains FIBRESTREAM
                        ['transaction-type' => 'credit'],
                        ['contains' => 'FIBRESTREAM'],
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        //contains PAYPAL
                        ['contains' => 'PAYPAL'],
                        //contains VOIP
                        ['contains' => 'VOIP'],
                        //contains MS
                        ['contains' => 'MS'],
                    ],
                    [
                        //credit
                        ['transaction-type' => 'credit'],
                        //starts with 407ETR
                        ['starts-with' => '407ETR'],
                    ],
                    //credit contains FIDO Mobile
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'FIDO'],
                        ['contains' => 'Mobile'],
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'RINGCENTRAL'],
                    ],
                ],
            ],
            'TR' => [
                'name' => 'Travel Expenses',
                'rules' => [
                    [
                        //credit contains PAYPAL contains *UBER
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => '*UBER'],
                    ]
                ],
            ],
            'OI' => [
                'name' => 'Owner Investment',
                'rules' => [
                    
                ],
            ],
            'BC' => [
                'name' => 'Bank Charges & Interests',
                'rules' => [
                    [
                        //credit Contains ANNUAL contains FEE minimum 119 maximum 121
                        ['transaction-type' => 'credit'],
                        ['contains' => 'ANNUAL'],
                        ['contains' => 'FEE'],
                        ['min-amount' => 119],
                        ['max-amount' => 121],
                    ]
                ],
            ],
            'SO' => [
                'name' => 'Software',
                'rules' => [
                    [
                        ['transaction-type' => 'credit'],
                        //starts with PAYPAL
                        ['starts-with' => 'PAYPAL'],
                        ['contains' => 'ENVATO MKPL'],
                    ],
                    //credit containts CONTROL containts PANEL containts SOLUTIONS
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'CONTROL'],
                        ['contains' => 'PANEL'],
                        ['contains' => 'SOLUTION'],
                    ],
                    [
                        //credit contains GOOGLE*PLAY
                        ['transaction-type' => 'credit'],
                        ['contains' => 'GOOGLE*PLAY'],
                    ],
                    //credit starts with ADOBE
                    [
                        ['transaction-type' => 'credit'],
                        ['starts-with' => 'ADOBE'],
                    ],
                    //credit contains SLACK
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'SLACK'],
                        ['contains' => 'TEZV'],
                    ],
                    //credit contains DropboxIncCADCardsPro
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'DropboxIncCADCardsPro'],
                    ],
                    [
                        //credit contains GOOGLE *Play
                        ['transaction-type' => 'credit'],
                        ['contains' => 'GOOGLE'],
                        ['contains' => 'Play'],
                    ],
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'PAYPAL'],
                        ['contains' => 'MICROSOFT'],
                    ],
                    [
                        [
                            ['transaction-type' => 'credit'],
                            ['contains' => 'ZOIPER'],
                        ],
                    ]
                ],
            ],
            'PE' => [
                'name' => 'Transfer to Personal Account',
                'rules' => [
                    
                ],
            ],
            'RE' => [
                'name' => 'Refund',
                'rules' => [
                    
                ],
            ],
            'RT' => [
                'name' => 'Rent',
                'rules' => [
                    
                ],
            ],
            'CP' => [
                'name' => 'CPP Payment',
                'rules' => [
                    
                ],
            ],
            'HS' => [
                'name' => 'HST Payment',
                'rules' => [
                    
                ],
            ],
            'OF' => [
                'name' => 'Office Supplies',
                'rules' => [
                    [
                        // credit transaction
                        ['transaction-type' => 'credit'],
                        //starts wtih paypal
                        ['starts-with' => 'PAYPAL'],
                        //ends with EBAY
                        ['contains' => 'EBAY'],

                    ],
                    [
                        // credit transaction
                        ['transaction-type' => 'credit'],
                        ['contains' => 'ALIEXPRESS'],
                    ],
                    [
                        //credit transaction
                        ['transaction-type' => 'credit'],
                        ['contains' => 'AMZN'],
                        ['contains' => 'Mktp'],
                    ],
                    //credit contains STAPLES CONTAINS BUSINESS DEPOT
                    [
                        ['transaction-type' => 'credit'],
                        ['contains' => 'STAPLES'],
                        ['contains' => 'BUSINESS DEPOT'],
                    ],
                ],
            ],
        ];
    }

}
