<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ItemPrice;
use App\Item;
use App\Branch;

class SetItemPriceTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:itemprice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $branches = Branch::orderBy('id')->get(['id'])->pluck('id');
        $items = Item::get();
        $line = 0;
        $kosong = 0;
        foreach ($items as $key => $item) {
            foreach ($branches as $key => $id_branch) {
                $null_flag = 0;
                if($item->nm_price == null)
                {
                    $null_flag++;
                }
                if($item->m_price == null)
                {
                    $null_flag++;
                }

                if($null_flag == 2) {
                    //gak ada harga
                    $kosong++;
                    $line++;
                }
                else {
                    $item_price = ItemPrice::where('branch_id',$id_branch)
                                    ->where('item_id',$item->id)->first();
                    if($item_price) {
                        $item_price->nm_price = $item->nm_price == null ? 0 : $item->nm_price;
                        $item_price->m_price = $item->m_price == null ? 0 : $item->m_price;
                    }
                    else {
                        $item_price = new ItemPrice();
                        $item_price->item_id = $item->id;
                        $item_price->branch_id = $id_branch;
                        $item_price->nm_price = $item->nm_price == null ? 0 : $item->nm_price;
                        $item_price->m_price = $item->m_price == null ? 0 : $item->m_price;
                        $item_price->created_by = 0;
                    }

                    $item_price->save();
                    $line++;
                }

                $this->info($line);
            }
        }
        $this->info($kosong);
    }
}
