<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AssetReminder;
use App\Models\LoadhseetTarget;
use App\Models\Loadsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AssetPerformance extends Controller
{
    //
    public function index()
    {
        return view('main.asset_performance.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);
        $latestTarget = LoadhseetTarget::latest()->first();

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('asset', function ($data) {
                return $data->asset_id . '-' . $data->asset->name . '-' . $data->asset->serial_number ?? null;
            })
            ->addColumn('PerformanceRate', function ($data) use ($latestTarget) {
                $percentage = (int) (($data->total_loadsheet / $latestTarget->target) * 100);
                $color = 'success';
                if ($percentage < 50) {
                    $color = 'danger';
                } elseif ($percentage >= 50 && $percentage < 80) {
                    $color = 'warning';
                }

                return '<div class="progress">
                    <div class="progress-bar bg-' . $color . '" role="progressbar" style="width: ' . $percentage . '%;" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">' . $percentage . '%</div>
                    </div>';
            })

            ->addColumn('Expenses', function ($data) {
                return "expenses" ?? null;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'asset_id',
            'loadsheet',
        ];
        $keyword = $request->search['value'] ?? '';

        $data = Loadsheet::selectRaw('asset_id, SUM(loadsheet) as total_loadsheet')
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->groupBy('asset_id');

        return $data;
    }


    public function create(Request $request)
    {
        return view('main.asset_performance.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data = LoadhseetTarget::create($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan! ' . $th->getMessage(),
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = AssetReminder::findByEncryptedId($id);

        return view('main.asset_reminder.edit', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $data = AssetReminder::findByEncryptedId($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal diperbarui! ' . $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = AssetReminder::findByEncryptedId($id);
            $data->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal dihapus! ' . $th->getMessage(),
            ]);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                $decryptedIds = [];
                foreach ($ids as $encryptedId) {
                    $decryptedIds[] = Crypt::decrypt($encryptedId);
                }

                $delete = AssetReminder::whereIn('id', $decryptedIds)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }
}
