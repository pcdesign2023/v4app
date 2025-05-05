@extends('layouts.master')

@section('css')
    <style>
        .chart-container {
            width: 100%;
            min-height: 400px;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Quality Control</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Anomaly & Conformity Charts</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- Row for Charts -->
    <div class="row">
        <!-- Anomaly Chart -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title text-center">Anomalies Stacked Column Chart</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4 justify-content-center text-center">
                        <div class="col-md-3">
                            <label>Start Date:</label>
                            <input type="date" id="startDateAnomaly" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>End Date:</label>
                            <input type="date" id="endDateAnomaly" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-center">
                            <button id="filterAnomalyBtn" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>

                    <div class="chart-container">
                        <canvas id="anomalySummaryChart"></canvas>
                    </div>

                </div>
            </div>
        </div>

        <!-- Conformity Chart -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title text-center">Total Conformities Line Chart</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row mb-4 justify-content-center text-center">
                            <div class="col-md-3">
                                <label>Start Date:</label>
                                <input type="date" id="startDateConformity" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>End Date:</label>
                                <input type="date" id="endDateConformity" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="filterConformityBtn" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="conformityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Label Anomaly Chart (New) -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title text-center">Total Non-Conformities by Label</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row mb-4 justify-content-center text-center">
                            <div class="col-md-3">
                                <label>Start Date:</label>
                                <input type="date" id="startDateLabelAnomaly" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>End Date:</label>
                                <input type="date" id="endDateLabelAnomaly" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="filterLabelAnomalyBtn" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="labelAnomalyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- RespDefaut Chart -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title text-center">Total Non-Conformities by RespDefaut</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row mb-4 justify-content-center text-center">
                            <div class="col-md-3">
                                <label>Start Date:</label>
                                <input type="date" id="startDateResp" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label>End Date:</label>
                                <input type="date" id="endDateResp" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="filterRespBtn" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="respDefautChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Row closed -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var respDefautChart; // Store chart instance globally

            function fetchRespDefautData(startDate = '', endDate = '') {
                console.log("Fetching Data with:", { start_date: startDate, end_date: endDate });

                $.ajax({
                    url: "{{ route('fetchRespDefautData') }}", // Ensure the route name is correct
                    method: "GET",
                    data: { start_date: startDate, end_date: endDate }, // Pass date filters
                    success: function(response) {
                        console.log("Response Received:", response); // Debugging response

                        var respDefautDates = response.respDefautDates || [];
                        var respDefautData = response.respDefautData || {};

                        // Handle case where no data is returned
                        if (respDefautDates.length === 0) {
                            alert("No data found for the selected date range.");
                        }

                        var respDefautDatasets = [];
                        var colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
                        var index = 0;

                        Object.keys(respDefautData).forEach(function (key) {
                            respDefautDatasets.push({
                                label: key,
                                data: respDefautData[key],
                                backgroundColor: colors[index % colors.length],
                                borderWidth: 1
                            });
                            index++;
                        });

                        var ctx4 = document.getElementById('respDefautChart').getContext('2d');

                        // Destroy previous chart instance if exists to avoid duplication
                        if (respDefautChart) {
                            respDefautChart.destroy();
                        }

                        // Create new Chart.js instance
                        respDefautChart = new Chart(ctx4, {
                            type: 'bar',
                            data: {
                                labels: respDefautDates,
                                datasets: respDefautDatasets
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'top' },
                                    title: { display: true, text: 'Total Non-Conformities by RespDefaut' }
                                },
                                scales: {
                                    x: {
                                        stacked: true,
                                        ticks: { autoSkip: true, maxRotation: 45, minRotation: 0 }
                                    },
                                    y: { stacked: true, beginAtZero: true }
                                }
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching RespDefaut data:", error);
                        alert("Error fetching data. Please try again.");
                    }
                });
            }

            // Initial Fetch (without date filters)
            fetchRespDefautData();

            // Event listener for Filter Button
            $("#filterRespBtn").on("click", function () {
                var startDate = $("#startDateResp").val();
                var endDate = $("#endDateResp").val();

                // Ensure both dates are selected
                if (!startDate || !endDate) {
                    alert("Please select both start and end dates.");
                    return;
                }

                fetchRespDefautData(startDate, endDate);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var conformityChart;

            function fetchConformityChart(startDate = '', endDate = '') {
                $.ajax({
                    url: "{{ route('fetchConformityChart') }}",
                    method: "GET",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        const conformityDates = response.conformityDates || [];
                        const conformityPercentages = response.conformityPercentages || [];

                        if (conformityDates.length === 0) {
                            alert("No data found for selected date range.");
                        }

                        // Destroy existing chart
                        if (conformityChart) {
                            conformityChart.destroy();
                        }

                        const ctx = document.getElementById('conformityChart').getContext('2d');
                        conformityChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: conformityDates,
                                datasets: [{
                                    label: 'Non-Conformity Percentage (%)',
                                    data: conformityPercentages,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 2,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'top' },
                                    title: { display: true, text: 'Non-Conformity Percentage Over Time' }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return value + "%";
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching conformity chart data:", error);
                    }
                });
            }

            // Initial Load
            fetchConformityChart();

            // Filter Button Action
            $("#filterConformityBtn").on("click", function () {
                const startDate = $("#startDateConformity").val();
                const endDate = $("#endDateConformity").val();

                if (!startDate || !endDate) {
                    alert("Please select both start and end dates.");
                    return;
                }

                fetchConformityChart(startDate, endDate);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function generateColorArray(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    let color = '#';
                    const letters = '0123456789ABCDEF';
                    for (let j = 0; j < 6; j++) {
                        color += letters[Math.floor(Math.random() * 16)];
                    }
                    colors.push(color);
                }
                return colors;
            }

            function fetchLabelAnomalySummary(startDate = '', endDate = '') {
                $.ajax({
                    url: "{{ route('fetchLabelAnomalyData') }}",
                    method: "GET",
                    data: { start_date: startDate, end_date: endDate },
                    success: function(response) {
                        const labels = response.labels;
                        const data = response.data;
                        const colors = generateColorArray(labels.length);

                        // Destroy existing chart
                        if (window.labelSummaryChartInstance) {
                            window.labelSummaryChartInstance.destroy();
                        }

                        const ctx = document.getElementById('labelAnomalyChart').getContext('2d');
                        window.labelSummaryChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Non-Conformities per Label',
                                    data: data,
                                    backgroundColor: colors
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                    title: { display: true, text: 'Total Non-Conformities by Label (Aggregated)' }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Error fetching summary data:", xhr);
                    }
                });
            }

            // Initial load
            fetchLabelAnomalySummary();

            // Filter button click
            $('#filterLabelAnomalyBtn').on('click', function () {
                const startDate = $('#startDateLabelAnomaly').val();
                const endDate = $('#endDateLabelAnomaly').val();

                if (!startDate || !endDate) {
                    alert("Please select both start and end dates.");
                    return;
                }

                fetchLabelAnomalySummary(startDate, endDate);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var summaryChart;

            function generateColorArray(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    let color = '#';
                    const letters = '0123456789ABCDEF';
                    for (let j = 0; j < 6; j++) {
                        color += letters[Math.floor(Math.random() * 16)];
                    }
                    colors.push(color);
                }
                return colors;
            }

            function fetchAnomalySummary(startDate = '', endDate = '') {
                $.ajax({
                    url: "{{ route('fetchAnomalySummary') }}",
                    method: "GET",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        const labels = response.labels;
                        const data = response.data;
                        const colors = generateColorArray(labels.length);

                        if (summaryChart) summaryChart.destroy();

                        const ctx = document.getElementById('anomalySummaryChart').getContext('2d');
                        summaryChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Anomalies per Label',
                                    data: data,
                                    backgroundColor: colors
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Anomalies Summary (Aggregated by Label)'
                                    },
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading anomaly summary chart:", xhr);
                    }
                });
            }

            // Initial load
            fetchAnomalySummary();

            // Filter button click
            $('#filterAnomalyBtn').on('click', function () {
                const startDate = $('#startDateAnomaly').val();
                const endDate = $('#endDateAnomaly').val();

                if (!startDate || !endDate) {
                    alert("Please select both start and end dates.");
                    return;
                }

                fetchAnomalySummary(startDate, endDate);
            });
        });
    </script>





@endsection
