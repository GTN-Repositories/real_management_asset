<?php

namespace App\Imports;

use App\Models\CategoryItem;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportItem implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new Item([
            'part' => $row['part_number'],
            'name' => $row['nama'],
            'category_id' => Crypt::decrypt(CategoryItem::where('name', $row['kategori'])->value('id')),
            'code' => $row['kode_barang'],
            'status' => $row['status'],
            'brand' => $row['merek'],
            'stock' => $row['stock'],
            'price' => $row['harga'],
            'no_invoice' => $row['nomor_invoice'],
            'supplier_name' => $row['nama_supplier'],
            'supplier_addrees' => $row['alamat_supplier'],
        ]);
    }
}
