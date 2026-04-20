<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Exports\MultiTableExport;

class DocController extends Controller
{
    public function index()
    {
        return view('backend.settings.docupload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'doc_file' => 'required|file'
        ]);

        $file = $request->file('doc_file');

        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(5) . '.' . $extension;

        $folderPath = public_path('uploads/docs');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $file->move($folderPath, $filename);

        $fullPath = $folderPath . '/' . $filename;

        $allTables = [];

        $zip = new \ZipArchive;

        if ($zip->open($fullPath) === TRUE) {

            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            $dom = new \DOMDocument();
            $dom->loadXML($xml);

            $tables = $dom->getElementsByTagName('tbl');

            $tableIndex = 1;

            foreach ($tables as $table) {

                $tableData = [];

                $rows = $table->getElementsByTagName('tr');

                foreach ($rows as $row) {

                    $cells = $row->getElementsByTagName('tc');
                    $rowData = [];

                    foreach ($cells as $cell) {

                        $texts = $cell->getElementsByTagName('t');
                        $cellText = '';

                        foreach ($texts as $text) {
                            $cellText .= $text->nodeValue;
                        }

                        $rowData[] = trim($cellText);
                    }

                    if (!empty(array_filter($rowData))) {
                        $tableData[] = $rowData;
                    }
                }

                if (!empty($tableData)) {
                    $allTables["Table_" . $tableIndex] = $tableData;
                    $tableIndex++;
                }
            }
        }

        if (empty($allTables)) {
            return back()->with('error', 'No table data found in document.');
        }

        return Excel::download(
            new MultiTableExport($allTables),
            'converted_' . time() . '.xlsx'
        );
    }
}
