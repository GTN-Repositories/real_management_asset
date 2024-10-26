<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\CategoryForm;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class FormController extends Controller
{
    public function index()
    {
        return view('main.form.index');
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $checkbox =
                    '<div class="custom-control custom-checkbox">
                    <input class="custom-control-input checkbox" id="checkbox' .
                    $data->id .
                    '" type="checkbox" value="' .
                    $data->id .
                    '" />
                    <label class="custom-control-label" for="checkbox' .
                    $data->id .
                    '"></label>
                </div>';

                return $checkbox;
            })
            ->addColumn('question', function ($data) {
                return $data->question ?? null;
            })
            ->addColumn('type', function ($data) {
                return $data->type ?? null;
            })
            ->addColumn('category', function ($data) {
                return $data->categoryFrom->name ?? null;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-m-Y');
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0);" class="btn btn-primary btn-sm me-1" title="Edit Data" onclick="editData(\'' . $data->id . '\')"><i class="ti ti-pencil"></i></a>';
                $btn .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Hapus Data" onclick="deleteData(\'' . $data->id . '\')"><i class="ti ti-trash"></i></a>';
                $btn .= '</div>';

                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'question',
            'type',
            'category_form_id',
            'created_at',
        ];

        $keyword = $request->search['value'];

        $data = Form::orderBy('created_at', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        return $data;
    }


    public function create()
    {
        $relation = CategoryForm::all();
        return view('main.form.create', compact('relation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                if (!is_numeric($data['category_form_id'])) {
                    try {
                        $data['category_form_id'] = Crypt::decrypt($data['category_form_id']);
                    } catch (\Exception $e) {
                        $categoryForm = CategoryForm::firstOrCreate(['name' => $data['category_form_id']]);
                        $decryptId = Crypt::decrypt($categoryForm->id);
                        $data['category_form_id'] = $decryptId;
                    }
                }

                // dd($data['category_form_id']);
                $data = Form::create($data);

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
        $data = Form::findByEncryptedId($id);
        $relation = CategoryForm::all();
        return view('main.form.edit', compact('data', 'relation'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                if (!is_numeric($data['category_form_id'])) {
                    try {
                        $data['category_form_id'] = Crypt::decrypt($data['category_form_id']);
                    } catch (\Exception $e) {
                        $categoryForm = CategoryForm::firstOrCreate(['name' => $data['category_form_id']]);
                        $decryptId = Crypt::decrypt($categoryForm->id);
                        $data['category_form_id'] = $decryptId;
                    }
                }
                $data = Form::findByEncryptedId($id)->update($data);

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
            $data = Form::findByEncryptedId($id);
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

                $delete = Form::whereIn('id', $decryptedIds)->delete();

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
