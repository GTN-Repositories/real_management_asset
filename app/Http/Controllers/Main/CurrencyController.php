<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index()
    {
        $view = 'invoice';

        return view('main.currency.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('code', function ($data) {
                return $data->code;
            })
            ->addColumn('symbol', function ($data) {
                return $data->symbol;
            })
            ->addColumn('exchange', function ($data) {
                return 'Rp '. number_format($data->exchange, 0, ',', '.');
            })
            ->addColumn('created_at', function ($data) {
                return (isset($data->created_at)) ? $data->created_at->diffForHumans() : null;
            })
            ->addColumn('updated_at', function ($data) {
                return (isset($data->updated_at)) ? $data->updated_at->diffForHumans() : null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'code',
            'symbol',
            'exchange',
            'created_at',
            'updated_at',
        ];

        $keyword = $request->keyword;

        $data = MdCurrency::orderBy('name', 'asc')
                    ->select($columns)
                    ->where(function($query) use ($keyword, $columns) {
                        if ($keyword != '') {
                            foreach ($columns as $column) {
                                $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                            }
                        }
                    })
                    ->get();
        
        return $data;
    }

    public function syncCurrency()
    {
        // https://v6.exchangerate-api.com/v6/a7091fda12481aafe62e3fc1/codes
        // https://v6.exchangerate-api.com/v6/a7091fda12481aafe62e3fc1/pair/EUR/IDR
        // https://v6.exchangerate-api.com/v6/a7091fda12481aafe62e3fc1/latest/USD

        $api_key = '66394d107e78158c7eae2ffc';
        $code_url = "https://v6.exchangerate-api.com/v6/$api_key/codes";
        $latest_url = "https://v6.exchangerate-api.com/v6/$api_key/latest/IDR";

        try {
            $checkCode = Http::get($code_url)->json();

            // dd($checkCode['supported_codes']);
            foreach ($checkCode['supported_codes'] as $key => $value) {
                $code_currency = $value[0];
                $name = $value[1];

                $checkCodeCurrency = Currency::where('code', $code_currency)->first();
                if (!$checkCodeCurrency) {
                    $currency = new Currency();
                    $currency->code = $code_currency;
                    $currency->name = $name;
                    $currency->symbol = '';
                    $currency->exchange = 0;
                    $currency->save();
                }
            }

            // CONVERT TO IDR
            $updateExcange = Http::get($latest_url)->json();
            // dd($updateExcange['conversion_rates']);
            foreach ($updateExcange['conversion_rates'] as $key => $value) {
                $code_currency_exchange = $key;
                $value_exchange = $value;
                $convert_to_id = 1 / $value;

                $checkCurrencyExchange = Currency::where('code', $code_currency_exchange)->first();
                if ($checkCurrencyExchange == 'IDR') {
                    $currency_exchange = Currency::where('code', $code_currency_exchange)->first();
                    $currency_exchange->exchange = $value_exchange;
                    $currency_exchange->save();
                }else{
                    $currency_exchange = Currency::where('code', $code_currency_exchange)->first();
                    $currency_exchange->exchange = $convert_to_id;
                    $currency_exchange->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Singkronisasi Data Berhasil',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Singkronisasi Data Gagal',
            ]);
        }
    }
}
