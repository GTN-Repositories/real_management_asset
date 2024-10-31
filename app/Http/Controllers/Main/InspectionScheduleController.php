<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Form;
use App\Models\InspectionSchedule;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InspectionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->data($request);

        return view('main.inspection_schedule.index', compact('data'));
    }

    public function data(Request $request)
    {
        $columns = [
            'id',
            'name',
            'type',
            'unit_id',
            'note',
            'date',
        ];

        $keyword = $request->search;
        $start_date = ($request->start_date != '') ? $request->start_date . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d'). ' 00:00:00';
        $end_date = ($request->end_date != '') ? $request->end_date . ' 23:59:59' : now()->endOfMonth()->format('Y-m-d'). ' 23:59:59';
        $type = $request->type;

        $data = InspectionSchedule::orderBy('created_at', 'asc')
            ->select($columns)
            ->whereBetween('date', [$start_date, $end_date])
            ->where(function ($query) use ($type, $keyword, $columns) {
                if ($type != '') {
                    $query->where('type', $type);
                }

                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        foreach ($data as $key => $value) {
            $value['start'] = $value['date']. ' 00:00:00';
            $value['end'] = $value['date']. ' 23:59:59';
        }

        return $data;
    }

    public function create()
    {
        return view('main.inspection_schedule.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($request) {
                $unit_id = Crypt::decrypt($request->input('unit_id'));

                $schedule = InspectionSchedule::create([
                    'name' => $request->input('name'),
                    'type' => $request->input('type'),
                    'unit_id' => $unit_id,
                    'note' => $request->input('note')
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil ditambahkan!',
                    'schedule_id' => $schedule->id,
                ]);
            });
        } catch (\Exception $e) {
            // Menangani error jika terjadi kegagalan
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan! ' . $e->getMessage(),
            ]);
        }
    }

    public function show(string $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $schedule = InspectionSchedule::findOrFail($decryptedId);
        $questionsGroupedByCategory = Form::where('type', $schedule->type)
            ->with('categoryFrom')
            ->get()
            ->groupBy('category_form_id');

        $categoryNames = [];
        foreach ($questionsGroupedByCategory as $categoryId => $forms) {
            if ($forms->first()->categoryFrom) {
                $categoryNames[$categoryId] = $forms->first()->categoryFrom->name;
            }
        }
        $questionCount = Form::where('type', $schedule->type)->count();
        return view('main.inspection_schedule.detail_inspection', compact('schedule', 'questionsGroupedByCategory', 'questionCount'));
    }

    public function showQuizDetails($scheduleId) {}
}