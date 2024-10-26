<?php

namespace Database\Seeders;

use App\Models\CategoryForm;
use App\Models\Form;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function PHPSTORM_META\type;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryForms = [
            [
                'name' => 'PENGECEKAN DOKUMEN KENDARAAN'
            ],
            [
                'name' => 'PENGECEKAN BODY KENDARAAN'
            ],
        ];

        foreach ($categoryForms as $categoryForm) {
            CategoryForm::create($categoryForm);
        }

        $questions = [
            [
                'question' => 'Persamaan No. Mesin pada kendaraan dan dokumen',
                'type' => 'p2h',
                'category_form_id' => 1
            ],
            [
                'question' => 'Legalitas STNK',
                'type' => 'p2h',
                'category_form_id' => 1
            ],
            [
                'question' => 'Buku Manual atau Buku Service Dealer',
                'type' => 'p2h',
                'category_form_id' => 1
            ],
            [
                'question' => 'Body Panel dan Bumper (goresan, penyok)',
                'type' => 'p2h',
                'category_form_id' => 2
            ],
            [
                'question' => 'Karpet Karet (tidak robek atau hilang)',
                'type' => 'p2h',
                'category_form_id' => 2
            ],
            [
                'question' => 'Karpet Dasar (bebas dari noda, air, dan bau)',
                'type' => 'p2h',
                'category_form_id' => 2
            ],
            [
                'question' => 'Kaca mobil bebas jamur',
                'type' => 'p2h',
                'category_form_id' => 2
            ],
            [
                'question' => 'Kaca film bebas goresan',
                'type' => 'p2h',
                'category_form_id' => 2
            ],



            [
                'question' => 'Persamaan No. Mesin pada kendaraan dan dokumen',
                'type' => 'pm',
                'category_form_id' => 1
            ],
            [
                'question' => 'Legalitas STNK',
                'type' => 'pm',
                'category_form_id' => 1
            ],
            [
                'question' => 'Buku Manual atau Buku Service Dealer',
                'type' => 'pm',
                'category_form_id' => 1
            ],
            [
                'question' => 'Body Panel dan Bumper (goresan, penyok)',
                'type' => 'pm',
                'category_form_id' => 2
            ],
            [
                'question' => 'Karpet Karet (tidak robek atau hilang)',
                'type' => 'pm',
                'category_form_id' => 2
            ],
            [
                'question' => 'Karpet Dasar (bebas dari noda, air, dan bau)',
                'type' => 'pm',
                'category_form_id' => 2
            ],
            [
                'question' => 'Kaca mobil bebas jamur',
                'type' => 'pm',
                'category_form_id' => 2
            ],
            [
                'question' => 'Kaca film bebas goresan',
                'type' => 'pm',
                'category_form_id' => 2
            ],
        ];

        foreach ($questions as $question) {
            Form::create($question);
        }
    }
}
