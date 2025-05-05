<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Fabrication;
use App\Models\ConformityDetail;
use App\Models\FabOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FabricationController extends Controller
{
    /**
     * Store a newly created fabrication record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'OFID' => 'required|exists:fab_orders,OFID',
            'Lot_Jus' => 'nullable|string',
            'Valid_date' => 'nullable|date',
            'effectif_Reel' => 'nullable|integer',
            'date_fabrication' => 'nullable|date',
            'Pf_Qty' => 'nullable|integer|min:0',
            'Sf_Qty' => 'nullable|integer|min:0',
            'Set_qty' => 'nullable|integer|min:0',
            'Tester_qty' => 'nullable|integer|min:0',
            'Comment_chaine' => 'nullable|string',
            'End_Fab_date' => 'nullable|string',
        ]);

        // ✅ Ensure numeric fields have default values (0 if NULL)
        Fabrication::create([
            'OFID' => $request->input('OFID'),
            'Lot_Jus' => $request->input('Lot_Jus'),
            'Valid_date' => $request->input('Valid_date'),
            'effectif_Reel' => $request->input('effectif_Reel', 0), // Default to 0 if NULL
            'date_fabrication' => $request->input('date_fabrication'),
            'End_Fab_date' => $request->input('End_Fab_date'),
            'Pf_Qty' => $request->input('Pf_Qty', 0), // Default to 0 if NULL
            'Sf_Qty' => $request->input('Sf_Qty', 0), // Default to 0 if NULL
            'Set_qty' => $request->input('Set_qty', 0), // Default to 0 if NULL
            'Tester_qty' => $request->input('Tester_qty', 0), // Default to 0 if NULL
            'Comment_chaine' => $request->input('Comment_chaine'),
        ]);
        // ✅ Update the corresponding FabOrder `Statut_of` field to "En cours"
        FabOrder::where('OFID', $request->input('OFID'))->update(['Statut_of' => 'En cours']);

        DB::commit();

        return redirect()->route('fab_chain.index')->with('success', 'Déclaration enregistré avec success 👍.');
    }
    public function updateHistory(Request $request, $id)
    {
        $request->validate([
            'Lot_Jus' => 'nullable|string|max:255',
            'Valid_date' => 'nullable|date',
            'effectif_Reel' => 'nullable|string|max:255',
            'Comment_chaine' => 'nullable|string|max:1000',
            'Pf_Qty' => 'nullable|numeric|min:0',
            'Sf_Qty' => 'nullable|numeric|min:0',
            'Set_qty' => 'nullable|numeric|min:0',
            'Tester_qty' => 'nullable|numeric|min:0',
        ]);

        $record = Fabrication::findOrFail($id);

        $record->Lot_Jus = $request->Lot_Jus;
        $record->Valid_date = $request->Valid_date;
        $record->effectif_Reel = $request->effectif_Reel;
        $record->Comment_chaine = $request->Comment_chaine;
        $record->Pf_Qty = $request->Pf_Qty;
        $record->Sf_Qty = $request->Sf_Qty;
        $record->Set_qty = $request->Set_qty;
        $record->Tester_qty = $request->Tester_qty;

        $record->save();

        return redirect()->back()->with('success', 'Historique mis à jour avec succès.');
    }
    public function getHistory($OFID)
{
    $decodedOFID = base64_decode($OFID); // Decode Base64-encoded OFID
    $history = Fabrication::where('OFID', $decodedOFID)->get();

    return response()->json($history);
}
    public function deleteById($id)
    {
        try {
            // Find the fabrication record
            $fabrication = Fabrication::find($id);

            // Check if the record exists
            if (!$fabrication) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fabrication record not found.'
                ], 404);
            }

            // Delete the record
            $fabrication->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Fabrication record deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the record: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:fabrication,id',
            'Lot_Jus' => 'nullable|string|max:255',
            'Valid_date' => 'nullable|date',
            'effectif_Reel' => 'nullable|integer|min:0',
            'date_fabrication' => 'nullable|date',
            'Pf_Qty' => 'nullable|integer|min:0',
            'Sf_Qty' => 'nullable|integer|min:0',
            'Set_qty' => 'nullable|integer|min:0',
            'Tester_qty' => 'nullable|integer|min:0',
            'Comment_chaine' => 'nullable|string',
        ]);

        // Find the fabrication record
        $fabrication = Fabrication::findOrFail($request->id);

        // Update the record
        $fabrication->update($request->all());

        return redirect()->route('fabrication.history')->with('success', 'Fabrication record updated successfully.');
    }



    public function history()
    {
        $fabrications = Fabrication::all(); // Fetch all fabrication records

        return view('fab_orders.history', compact('fabrications')); // Pass data to the history view
    }

    public function comparison()
    {
        $total = Fabrication::count(); // Get total fabrication orders

        $comparisons = FabOrder::leftJoinSub(
            Fabrication::select(
                'OFID',
                DB::raw('SUM(Pf_Qty) as fabrication_Pf_Qty'),
                DB::raw('SUM(Sf_Qty) as fabrication_Sf_Qty'),
                DB::raw('SUM(Set_qty) as fabrication_Set_qty'),
                DB::raw('SUM(Tester_qty) as fabrication_Tester_qty')
            )->groupBy('OFID'),
            'fabrication',
            'fab_orders.OFID',
            '=',
            'fabrication.OFID'
        )
            ->leftJoin('clients', 'clients.id', '=', 'fab_orders.client_id') // Join with clients table
            ->select(
                'fab_orders.OFID',
                'fab_orders.statut_of',
                'fab_orders.saleOrderId',
                'clients.name as client_name', // Select client name
                DB::raw('SUM(fab_orders.Pf_Qty) as fab_orders_Pf_Qty'),
                DB::raw('SUM(fab_orders.Sf_Qty) as fab_orders_Sf_Qty'),
                DB::raw('SUM(fab_orders.Set_qty) as fab_orders_Set_qty'),
                DB::raw('SUM(fab_orders.Tester_qty) as fab_orders_Tester_qty'),
                DB::raw('MIN(COALESCE(fabrication.fabrication_Pf_Qty, 0)) as fabrication_Pf_Qty'),
                DB::raw('MIN(COALESCE(fabrication.fabrication_Sf_Qty, 0)) as fabrication_Sf_Qty'),
                DB::raw('MIN(COALESCE(fabrication.fabrication_Set_qty, 0)) as fabrication_Set_qty'),
                DB::raw('MIN(COALESCE(fabrication.fabrication_Tester_qty, 0)) as fabrication_Tester_qty')
            )
            ->groupBy('fab_orders.OFID','fab_orders.saleOrderId', 'clients.name', 'fab_orders.statut_of') // Group by OFID and client name
            ->get();
        return view('fab_orders.comparison', compact('comparisons', 'total'));
    }
    public function comparison_sale_order()
    {
        $total = Fabrication::count(); // Get total fabrication orders

        $comparisons = FabOrder::leftJoinSub(
            Fabrication::select(
                'OFID',
                DB::raw('SUM(Pf_Qty) as fabrication_Pf_Qty'),
                DB::raw('SUM(Sf_Qty) as fabrication_Sf_Qty'),
                DB::raw('SUM(Set_qty) as fabrication_Set_qty'),
                DB::raw('SUM(Tester_qty) as fabrication_Tester_qty')
            )->groupBy('OFID'),
            'fabrication',
            'fab_orders.OFID',
            '=',
            'fabrication.OFID'
        )
            ->leftJoin('clients', 'clients.id', '=', 'fab_orders.client_id') // Join with clients table
            ->select(
                'fab_orders.saleOrderId',
                'clients.name as client_name', // Select client name
                DB::raw('SUM(fab_orders.Pf_Qty) as fab_orders_Pf_Qty'),
                DB::raw('SUM(fab_orders.Sf_Qty) as fab_orders_Sf_Qty'),
                DB::raw('SUM(fab_orders.Set_qty) as fab_orders_Set_qty'),
                DB::raw('SUM(fab_orders.Tester_qty) as fab_orders_Tester_qty'),
                DB::raw('SUM(COALESCE(fabrication.fabrication_Pf_Qty, 0)) as fabrication_Pf_Qty'),
                DB::raw('SUM(COALESCE(fabrication.fabrication_Sf_Qty, 0)) as fabrication_Sf_Qty'),
                DB::raw('SUM(COALESCE(fabrication.fabrication_Set_qty, 0)) as fabrication_Set_qty'),
                DB::raw('SUM(COALESCE(fabrication.fabrication_Tester_qty, 0)) as fabrication_Tester_qty')
            )
            ->groupBy('fab_orders.saleOrderId', 'clients.name')
            ->get();
        return view('fab_orders.comparison-sale-order', compact('comparisons', 'total'));
    }



    public function showChart()
    {
        $data = DB::table(DB::raw('(
        WITH ConformityData AS (
            SELECT
                DATE(created_at) AS creation_date,
                fk_OFID,
                SUM(Qty_NC) AS total_Qty_NC
            FROM conformity_details
            GROUP BY DATE(created_at), fk_OFID
        ),
        FabricationData AS (
            SELECT
                f.id AS fk_OFID,
                SUM(fb.Pf_Qty) AS total_Pf_Qty,
                SUM(fb.Sf_Qty) AS total_Sf_Qty,
                SUM(fb.Set_Qty) AS total_Set_Qty,
                SUM(fb.Tester_qty) AS total_Tester_qty
            FROM fab_orders f
            LEFT JOIN fabrication fb ON f.OFID = fb.OFID
            GROUP BY f.id
        )
        SELECT
            c.creation_date,
            SUM(c.total_Qty_NC) AS total_Qty_NC,
            COALESCE(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0) AS total_quantity,
            ROUND(
                (SUM(c.total_Qty_NC) / NULLIF(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0)) * 100, 2
            ) AS nc_percentage
        FROM ConformityData c
        LEFT JOIN FabricationData fb ON c.fk_OFID = fb.fk_OFID
        GROUP BY c.creation_date
        ORDER BY c.creation_date ASC
    ) as aggregated_data'))
            ->get();

        $dates = [];
        $percentages = [];

        foreach ($data as $row) {
            $dates[] = Carbon::parse($row->creation_date)->format('d/m/Y');
            $percentages[] = $row->nc_percentage;
        }

        return view('quality.chart', compact('dates', 'percentages'));
    }

    public function generateAnomalyCharts(Request $request)
    {
        // Step 1: Generate dynamic column names for anomalies
        $columnQuery = "SELECT GROUP_CONCAT(DISTINCT
                    CONCAT('MAX(CASE WHEN anomaly_name = \'', libele, '\' THEN total_Qty_NC ELSE 0 END) AS `', libele, '`'))
                AS columns
                FROM anomalies;";

        $columnResult = DB::select($columnQuery);
        $dynamicColumns = $columnResult[0]->columns ?? '';

        // Step 2: Construct the final dynamic SQL query for anomalies
        $finalQuery = "WITH ConformityData AS (
                        SELECT
                            DATE(created_at) AS creation_date,
                            fk_OFID,
                            SUM(Qty_NC) AS total_Qty_NC
                        FROM conformity_details
                        GROUP BY DATE(created_at), fk_OFID
                   ),
                   FabricationData AS (
                        SELECT
                            f.id AS fk_OFID,
                            SUM(fb.Pf_Qty) AS total_Pf_Qty,
                            SUM(fb.Sf_Qty) AS total_Sf_Qty,
                            SUM(fb.Set_Qty) AS total_Set_Qty,
                            SUM(fb.Tester_qty) AS total_Tester_qty
                        FROM fab_orders f
                        LEFT JOIN fabrication fb ON f.OFID = fb.OFID
                        GROUP BY f.id
                   )
                   SELECT
                        c.creation_date,
                        SUM(c.total_Qty_NC) AS total_Qty_NC,
                        COALESCE(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0) AS total_quantity,
                        ROUND(
                            (SUM(c.total_Qty_NC) / NULLIF(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0)) * 100, 2
                        ) AS nc_percentage
                   FROM ConformityData c
                   LEFT JOIN FabricationData fb ON c.fk_OFID = fb.fk_OFID
                   GROUP BY c.creation_date
                   ORDER BY c.creation_date ASC;";

        // Step 3: Execute the query for conformity percentages
        $conformityData = DB::select($finalQuery);

        $conformityDates = [];
        $conformityPercentages = [];

        foreach ($conformityData as $row) {
            $conformityDates[] = $row->creation_date;
            $conformityPercentages[] = round($row->nc_percentage, 2); // Round to 2 decimal places
        }

        // Step 4: Fetch anomaly data
        $anomalyQuery = "WITH ConformityData AS (
                        SELECT
                            DATE(c.created_at) AS creation_date,
                            a.libele AS anomaly_name,
                            SUM(c.Qty_NC) AS total_Qty_NC
                        FROM conformity_details c
                        JOIN anomalies a ON c.AnoId = a.AnoID
                        GROUP BY DATE(c.created_at), a.libele
                   )
                   SELECT
                        creation_date, $dynamicColumns,
                        SUM(total_Qty_NC) AS Total
                   FROM ConformityData
                   GROUP BY creation_date
                   ORDER BY creation_date ASC;";

        $anomalyDataRaw = DB::select($anomalyQuery);

        $dates = [];
        $anomalyData = [];

        foreach ($anomalyDataRaw as $row) {
            $dates[] = $row->creation_date;
            foreach ($row as $key => $value) {
                if ($key !== 'creation_date' && $key !== 'Total') {
                    $anomalyData[$key][] = $value;
                }
            }
        }

        return view('quality.chart', compact('dates', 'anomalyData', 'conformityDates', 'conformityPercentages'));
    }
public function generateAnomalyChartSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $params = [];
        $whereClause = '';

        if ($startDate && $endDate) {
            $whereClause = "WHERE DATE(c.created_at) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }

        $query = "
        SELECT
            a.libele AS anomaly_label,
            SUM(c.Qty_NC) AS total_Qty_NC
        FROM conformity_details c
        JOIN anomalies a ON c.AnoId = a.AnoID
        $whereClause
        GROUP BY a.libele
        ORDER BY total_Qty_NC DESC
    ";

        $results = DB::select($query, $params);

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = $row->anomaly_label;
            $data[] = $row->total_Qty_NC;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
    public function fetchLabelAnomalyData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = "SELECT de.label AS anomaly_label, SUM(c.Qty_NC) AS total_Qty_NC
              FROM conformity_details c
              JOIN default_entries de ON c.AnoID = de.AnoID";

        $params = [];

        if ($startDate && $endDate) {
            $query .= " WHERE DATE(c.created_at) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }

        $query .= " GROUP BY de.label ORDER BY total_Qty_NC DESC";

        $results = DB::select($query, $params);

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = $row->anomaly_label;
            $data[] = $row->total_Qty_NC;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function fetchRespDefautData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base SQL query
        $query = "SELECT
                DATE(c.created_at) AS creation_date,
                c.RespDefaut AS resp_defaut,
                SUM(c.Qty_NC) AS total_Qty_NC
              FROM conformity_details c";

        // Apply date filtering if both start and end dates are provided
        if ($startDate && $endDate) {
            $query .= " WHERE DATE(c.created_at) BETWEEN ? AND ?";
        }

        $query .= " GROUP BY DATE(c.created_at), c.RespDefaut
                ORDER BY creation_date ASC;";

        // Execute query with parameters if filtering is applied
        $dataRaw = $startDate && $endDate
            ? DB::select($query, [$startDate, $endDate])
            : DB::select($query);

        // Process data for JSON response
        $respDefautDates = [];
        $respDefautData = [];

        foreach ($dataRaw as $row) {
            if (!in_array($row->creation_date, $respDefautDates)) {
                $respDefautDates[] = $row->creation_date;
            }

            $respDefautData[$row->resp_defaut][] = $row->total_Qty_NC;
        }

        return response()->json([
            'respDefautDates' => $respDefautDates,
            'respDefautData' => $respDefautData
        ]);
    }
    // In FabricationController.php (or a new controller like ChartController.php)
    public function fetchConformityChart(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Handle optional date filtering
        $whereClause = "";
        $params = [];

        if ($startDate && $endDate) {
            $whereClause = "WHERE DATE(created_at) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }

        // SQL Query with date filtering
        $query = "WITH ConformityData AS (
                    SELECT
                        DATE(created_at) AS creation_date,
                        fk_OFID,
                        SUM(Qty_NC) AS total_Qty_NC
                    FROM conformity_details
                    $whereClause
                    GROUP BY DATE(created_at), fk_OFID
               ),
               FabricationData AS (
                    SELECT
                        f.id AS fk_OFID,
                        SUM(fb.Pf_Qty) AS total_Pf_Qty,
                        SUM(fb.Sf_Qty) AS total_Sf_Qty,
                        SUM(fb.Set_Qty) AS total_Set_Qty,
                        SUM(fb.Tester_qty) AS total_Tester_qty
                    FROM fab_orders f
                    LEFT JOIN fabrication fb ON f.OFID = fb.OFID
                    GROUP BY f.id
               )
               SELECT
                    c.creation_date,
                    SUM(c.total_Qty_NC) AS total_Qty_NC,
                    COALESCE(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0) AS total_quantity,
                    ROUND(
                        (SUM(c.total_Qty_NC) / NULLIF(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0)) * 100, 2
                    ) AS nc_percentage
               FROM ConformityData c
               LEFT JOIN FabricationData fb ON c.fk_OFID = fb.fk_OFID
               GROUP BY c.creation_date
               ORDER BY c.creation_date ASC;";

        // Execute query
        $conformityData = DB::select($query, $params);

        // Format response
        $conformityDates = [];
        $conformityPercentages = [];

        foreach ($conformityData as $row) {
            $conformityDates[] = $row->creation_date;
            $conformityPercentages[] = round($row->nc_percentage, 2);
        }

        return response()->json([
            'conformityDates' => $conformityDates,
            'conformityPercentages' => $conformityPercentages
        ]);
    }
// In FabricationController.php (or a new controller like ChartController.php)
    public function fetchConformityCharts(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Handle optional date filtering
        $whereClause = "";
        $params = [];

        if ($startDate && $endDate) {
            $whereClause = "WHERE DATE(created_at) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }

        // SQL Query with date filtering
        $query = "WITH ConformityData AS (
                    SELECT
                        DATE(created_at) AS creation_date,
                        fk_OFID,
                        SUM(Qty_NC) AS total_Qty_NC
                    FROM conformity_details
                    $whereClause
                    GROUP BY DATE(created_at), fk_OFID
               ),
               FabricationData AS (
                    SELECT
                        f.id AS fk_OFID,
                        SUM(fb.Pf_Qty) AS total_Pf_Qty,
                        SUM(fb.Sf_Qty) AS total_Sf_Qty,
                        SUM(fb.Set_Qty) AS total_Set_Qty,
                        SUM(fb.Tester_qty) AS total_Tester_qty
                    FROM fab_orders f
                    LEFT JOIN fabrication fb ON f.OFID = fb.OFID
                    GROUP BY f.id
               )
               SELECT
                    c.creation_date,
                    SUM(c.total_Qty_NC) AS total_Qty_NC,
                    COALESCE(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0) AS total_quantity,
                    ROUND(
                        (SUM(c.total_Qty_NC) / NULLIF(SUM(fb.total_Pf_Qty + fb.total_Sf_Qty + fb.total_Set_Qty + fb.total_Tester_qty), 0)) * 100, 2
                    ) AS nc_percentage
               FROM ConformityData c
               LEFT JOIN FabricationData fb ON c.fk_OFID = fb.fk_OFID
               GROUP BY c.creation_date
               ORDER BY c.creation_date ASC;";

        // Execute query
        $conformityData = DB::select($query, $params);

        // Format response
        $conformityDates = [];
        $conformityPercentages = [];

        foreach ($conformityData as $row) {
            $conformityDates[] = $row->creation_date;
            $conformityPercentages[] = round($row->nc_percentage, 2);
        }

        return response()->json([
            'conformityDates' => $conformityDates,
            'conformityPercentages' => $conformityPercentages
        ]);
    }

}
