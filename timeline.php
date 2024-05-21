<?php
session_start();
$userlevel_id = $_SESSION['userlevel_id'] ?? null;

function load_granttchart()
{
    $conn = open_connection();
    $userlevel_id = $_SESSION['userlevel_id'];
    $employee_id = $_SESSION['employee_id'];

    $where = "";

    if ($userlevel_id == 1) {
        $where  .= "";
    } else {
        $where  .= " AND tblticket.employee_id = '$employee_id'";
    }


    $sql = "SELECT tbljob.job, tblticket.task, tblsub_job.subjob, tblemployee.employee_name, tblemployee.employee_name,tblemployee.employee_nickname,
        tblticket.deadline_start, tblticket.deadline_end, tblticket.duration_start, tblticket.duration_end, tblticket_status.ticketstatus
        FROM tblticket
        LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
        LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
        LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
        LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
        WHERE 1 $where";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) <= 0) {
        $result = 0;
    }

    $conn->close();
    return $result;
}


if (!isset($_SESSION["is_login"])) {

    header('Location: ' . 'index.php');

    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <title>Timeline | Jirattin</title>

    <link href="css/chart.css" rel="stylesheet">
</head>

<body>

    <?php include "sidebar.php"; ?>

    <div class="main-contentticket">
        <?php include "header.php"; ?>

        <main>

            <!-- START YOUR CODE HERE -->

            <div class="chartCard">
                <div class="chartBox">
                    <div class="displayMonth">
                        <a href="timelinee.php"><button class="btnSwitch" id="timeline-link">Switch to Task</button></a>
                        <h2 id="currentMonth"></h2>
                    </div>
                    <canvas id="myChart">
                        <?php
                        $result = load_granttchart();
                        if ($result) {
                            $deadlineDatasets = [];
                            $durationDatasets = [];

                            while ($row = mysqli_fetch_assoc($result)) {
                                $deadlineStart = date('Y-m-d H:i:s', strtotime($row['deadline_start']));
                                $deadlineEnd = date('Y-m-d H:i:s', strtotime($row['deadline_end']));
                                $durationStart = date('Y-m-d H:i:s', strtotime($row['duration_start']));
                                $durationEnd = date('Y-m-d H:i:s', strtotime($row['duration_end']));

                                $deadlineDatasets[] = [
                                    'x' => [$deadlineStart, $deadlineEnd],
                                    'y' => $row['job'] . ': ' . $row['employee_nickname'],
                                    'color' => 'lightblue',
                                    'job' => $row['job'],
                                    'subjob' => $row['subjob'],
                                    'ticketstatus' => $row['ticketstatus'],
                                    'task' => $row['task']
                                ];

                                $durationDatasets[] = [
                                    'x' => [$durationStart, $durationEnd],
                                    'y' => $row['job'] . ': ' . $row['employee_nickname'],
                                    'color' => 'grey',
                                    'job' => $row['job'],
                                    'subjob' => $row['subjob'],
                                    'ticketstatus' => $row['ticketstatus'],
                                    'task' => $row['task']
                                ];
                            }

                            $deadlineDatasetsJson = json_encode($deadlineDatasets);
                            $durationDatasetsJson = json_encode($durationDatasets);
                        }
                        ?>
                    </canvas>
                    <div class="filterdates">
                        Start: <input id="start" type="date">
                        End: <input id="end" type="date">

                        <button onclick="filterDate()">Filter</button>
                        <button onclick="resetDate()">Reset</button>&nbsp;&nbsp;
                    </div>

                    <div class="filterdatesbtn">
                        <div class="filtersss">
                            <?php if ($userlevel_id == 1) { ?>
                                <select class="form-control select2" name="assignnametime" id="assignnametime">
                                    <option value="">Select Employee</option>
                                    <?php
                                    $select_query = mysqli_query($conn, "SELECT employee_name, employee_id FROM tblemployee ORDER BY employee_name ASC");
                                    while ($res = mysqli_fetch_array($select_query)) { ?>
                                        <option value="<?php echo $res['employee_id'] ?>">
                                            <?php echo $res['employee_name']; ?></option>
                                    <?php } ?>
                                </select>
                                <!-- <button class="btnsearchtimeline btn-warning" id="btnsearchtimeline"><i class="bi bi-search"></i></button> -->
                            <?php } ?>
                        </div>
                        <?php if ($userlevel_id == 1) { ?>
                            <button class="btnsearchtimeline btn-warning" onclick="filteremployee()">Filter</button>
                            <button class="btnsearchtimelineclear" onclick="clearSearchInputs()">Reset</button>
                        <?php } ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <button onclick="timeFrame(this)" value="day">Day</button>
                        <button onclick="timeFrame(this)" value="week">Week</button>
                        <button onclick="timeFrame(this)" value="month">Month</button>
                        <button onclick="timeFrame(this)" value="year">Year</button>
                    </div>
                </div>
            </div>



            <!-- END CODE -->

        </main>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
        <script>
            var ctx = document.getElementById('myChart').getContext('2d');

            let deadlineDatasetsJson = '<?php echo $deadlineDatasetsJson; ?>';
            let durationDatasetsJson = '<?php echo $durationDatasetsJson; ?>';

            let deadlineDatasets = JSON.parse(deadlineDatasetsJson);
            let durationDatasets = JSON.parse(durationDatasetsJson);

            const data = {
                datasets: [{
                        label: 'Tickets',
                        data: deadlineDatasets,
                        backgroundColor: (ctx) => ctx.raw.color,
                        borderColor: [
                            'rgb(173, 216, 230)'
                        ],
                        borderWidth: 1,
                        borderSkipped: false,
                        borderRadius: 10,
                        barPercentage: 0.5,
                        datalabels: {
                            display: true,
                        }
                    },
                    {
                        label: 'Tickets',
                        data: durationDatasets,
                        backgroundColor: (ctx) => ctx.raw.color,
                        borderColor: [
                            'rgb(173, 216, 230)'
                        ],
                        borderWidth: 1,
                        borderSkipped: false,
                        borderRadius: 10,
                        barPercentage: 0.5,
                        datalabels: {
                            display: true,
                        }
                    }
                ]
            };

            const todayLine = {
                id: 'todayLine',
                afterDatasetsDraw(chart, args, pluginOption) {
                    const {
                        ctx,
                        chartArea: {
                            top,
                            bottom,
                            left,
                            right
                        },
                        scales: {
                            x,
                            y
                        }
                    } = chart;

                    ctx.save();

                    if (x.getPixelForValue(new Date()) >= left && x.getPixelForValue(new Date()) <= right) {
                        ctx.beginPath();
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = 'rgba(169, 169, 169, 1)';
                        ctx.setLineDash([6, 6]);
                        ctx.moveTo(x.getPixelForValue(new Date()), top);
                        ctx.lineTo(x.getPixelForValue(new Date()), bottom);
                        ctx.stroke();
                        ctx.restore();
                        ctx.setLineDash([]);
                        ctx.beginPath();
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = 'rgba(169, 169, 169, 1)';
                        ctx.fillStyle = 'rgba(169, 169, 169, 1)';
                        ctx.moveTo(x.getPixelForValue(new Date()), top + 3);
                        ctx.lineTo(x.getPixelForValue(new Date()) - 6, top - 6);
                        ctx.lineTo(x.getPixelForValue(new Date()) + 6, top - 6);
                        ctx.closePath();
                        ctx.stroke();
                        ctx.fill();
                        ctx.restore();
                        ctx.font = 'bold 14px sans-serif';
                        ctx.fillStyle = 'black';
                        ctx.textAlign = 'center';
                        ctx.fillText('Today', x.getPixelForValue(new Date()), bottom + 15);
                        ctx.restore();
                    }
                }
            };

            const assignedTasks = {
                id: 'assignedTasks',
                afterDatasetsDraw(chart, args, pluginOption) {
                    const {
                        ctx,
                        data,
                        chartArea: {
                            top,
                            bottom,
                            left,
                            right
                        },
                        scales: {
                            x,
                            y
                        }
                    } = chart;
                    ctx.font = "bolder 20px sans-serif";
                    ctx.fillStyle = 'black';
                    ctx.textAlign = 'left';
                    ctx.restore();
                }
            }

            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
            const lastDay = new Date(currentYear, currentMonth, 0).getDate();

            const startDate = `${currentYear}-${currentMonth}-01`;
            const endDate = `${currentYear}-${currentMonth}-${lastDay}`;

            const config = {
                type: 'bar',
                data: {
                    datasets: [{
                        label: 'Deadlines',
                        data: deadlineDatasets,
                        backgroundColor: (ctx) => ctx.raw.color,
                        borderColor: 'black',
                        borderWidth: 1.5,
                        borderRadius: 10,
                        borderSkipped: false,
                        barPercentage: 1.29,
                        datalabels: {
                            align: 'center',
                            anchor: 'center',
                            offset: 4,
                            color: 'black',
                            formatter: (value, context) => {
                                const data = context.dataset.data[context.dataIndex];
                                const startDate = new Date(data.x[0]);
                                const endDate = new Date(data.x[1]);
                                const timeDifference = endDate - startDate;

                                const numTickets = durationDatasets.length;
                                const maxSize = 13.5;
                                const minSize = 8;

                                const fontSize = Math.min(maxSize, Math.max(minSize, (numTickets / 100) * maxSize));

                                context.font = `${fontSize}px sans-serif`;

                                const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                                const remainingHours = (timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60);

                                let formattedDuration;
                                if (days > 0) {
                                    formattedDuration = `${days} Day${days > 1 ? 's' : ''}`;
                                } else {
                                    formattedDuration = `${remainingHours} Hour${remainingHours > 1 ? 's' : ''}`;
                                }

                                return `${data.job}\nDeadline: ${formattedDuration}`;
                            },
                        },
                    }, {
                        label: 'Duration',
                        data: durationDatasets,
                        backgroundColor: (ctx) => ctx.raw.color,
                        borderWidth: 1.5,
                        borderColor: 'black',
                        borderRadius: 10,
                        borderSkipped: false,
                        barPercentage: 1.29,
                        datalabels: {
                            align: 'center',
                            anchor: 'center',
                            offset: 4,
                            color: 'black',
                            formatter: (value, context) => {
                                const data = context.dataset.data[context.dataIndex];
                                const startDate = new Date(data.x[0]);
                                const endDate = new Date(data.x[1]);
                                const timeDifference = endDate - startDate;

                                const numTickets = durationDatasets.length;
                                const maxSize = 13.5;
                                const minSize = 8;

                                const fontSize = Math.min(maxSize, Math.max(minSize, (numTickets / 100) * maxSize));

                                context.font = `${fontSize}px sans-serif`;

                                const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                                const remainingHours = (timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60);

                                let formattedDuration;
                                if (days > 0) {
                                    formattedDuration = `${days} Day${days > 1 ? 's' : ''}`;
                                } else {
                                    formattedDuration = `${remainingHours} Hour${remainingHours > 1 ? 's' : ''}`;
                                }

                                return `${data.job}\nDuration: ${formattedDuration}`;
                            },
                        },
                    }]
                },
                options: {
                    layout: {
                        padding: {
                            bottom: 20
                        }
                    },
                    indexAxis: 'y',
                    scales: {
                        x: {
                            position: 'top',
                            type: 'time',
                            time: {
                                displayFormats: {
                                    day: 'd'
                                },
                                minUnit: 'day'
                            },
                            min: startDate,
                            max: endDate
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            displayColors: false,
                            yAlign: 'bottom',
                            callbacks: {
                                label: (ctx) => {
                                    const data = ctx.raw;
                                    const startDate = new Date(data.x[0]);
                                    const endDate = new Date(data.x[1]);
                                    const formattedStartDate = startDate.toLocaleString([], {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                    });
                                    const formattedEndDate = endDate.toLocaleString([], {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                    });

                                    const deadlineDuration = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
                                    const actualDuration = Math.floor((new Date(data.duration_end) - new Date(data.duration_start)) / (1000 * 60 * 60 * 24));

                                    if (ctx.raw.color === 'grey') {
                                        return [`Duration: ${formattedStartDate} - ${formattedEndDate}`,
                                            `Status: ${data.ticketstatus}`
                                        ];
                                    } else {
                                        return [
                                            `Task Deadline: ${formattedStartDate} - ${formattedEndDate}`,
                                            `Subjob: ${data.subjob}`,
                                            `Status: ${data.ticketstatus}`
                                        ];
                                    }
                                },
                            }
                        }
                    }
                },

                plugins: [todayLine, assignedTasks, ChartDataLabels]
            };

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
            const originalDatapoints = deadlineDatasets.slice();
            const originalDatapointss = durationDatasets.slice();

            function filterDate() {
                const startInput = document.getElementById('start').value;
                const endInput = document.getElementById('end').value;

                if (!startInput || !endInput) {
                    console.error("Start and end dates are required");
                    return;
                }

                const startDate = new Date(startInput);
                let endDate = new Date(endInput);

                endDate.setHours(23, 59, 59, 999);
                endDate = new Date(endDate.getFullYear(), endDate.getMonth() + 1, 0, 23, 59, 59, 999);

                const filteredDatapoints = originalDatapoints.filter(data => {
                    const dataStartDate = new Date(data.x[0]);
                    const dataEndDate = new Date(data.x[1]);

                    dataEndDate.setHours(23, 59, 59, 999);

                    return dataStartDate <= endDate && dataEndDate >= startDate;
                });

                const filteredDurationDatapoints = originalDatapointss.filter(data => {
                    const dataStartDate = new Date(data.x[0]);
                    const dataEndDate = new Date(data.x[1]);

                    dataEndDate.setHours(23, 59, 59, 999);

                    return dataStartDate <= endDate && dataEndDate >= startDate;
                });

                const monthStart = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                const monthEnd = new Date(endDate.getFullYear(), endDate.getMonth() + 1, 0, 23, 59, 59, 999);

                myChart.config.options.scales.x.min = monthStart;
                myChart.config.options.scales.x.max = monthEnd;

                const currentMonthElement = document.getElementById("currentMonth");

                if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    const formattedStartDate = startDate.toLocaleDateString(undefined, options);
                    const formattedEndDate = endDate.toLocaleDateString(undefined, options);

                    currentMonthElement.innerText = formattedStartDate + ' - ' + formattedEndDate;
                } else {
                    console.error("Invalid date format");
                    currentMonthElement.innerText = "MONTH";
                }

                myChart.config.data.datasets[0].data = filteredDatapoints;
                myChart.config.data.datasets[1].data = filteredDurationDatapoints;
                myChart.update();

                if (filteredDatapoints.length > 0) {
                    const firstDataPoint = filteredDatapoints[0];
                    const scrollX = myChart.scales['x-axis-0'].getPixelForValue(firstDataPoint.x[0]);
                    myChart.options.plugins.tooltip.position = 'nearest';
                    myChart.options.plugins.tooltip.intersect = true;
                    myChart.options.plugins.tooltip.mode = 'index';
                    myChart.options.plugins.tooltip.callbacks.title = function() {
                        return '';
                    };
                    myChart.options.scales.x.min = firstDataPoint.x[0];
                    myChart.options.scales.x.max = firstDataPoint.x[1];
                    myChart.update();
                    myChart.resetZoom();
                    myChart.options.scales.x.min = undefined;
                    myChart.options.scales.x.max = undefined;
                    myChart.options.plugins.tooltip.mode = 'nearest';
                }

                if (filteredDurationDatapoints.length > 0) {
                    const firstDataPoint = filteredDurationDatapoints[0];
                    const scrollX = myChart.scales['x-axis-0'].getPixelForValue(firstDataPoint.x[0]);
                    myChart.options.plugins.tooltip.position = 'nearest';
                    myChart.options.plugins.tooltip.intersect = true;
                    myChart.options.plugins.tooltip.mode = 'index';
                    myChart.options.plugins.tooltip.callbacks.title = function() {
                        return '';
                    };
                    myChart.options.scales.x.min = firstDataPoint.x[0];
                    myChart.options.scales.x.max = firstDataPoint.x[1];
                    myChart.update();
                    myChart.resetZoom();
                    myChart.options.scales.x.min = undefined;
                    myChart.options.scales.x.max = undefined;
                    myChart.options.plugins.tooltip.mode = 'nearest';
                }
            }


            function resetDate() {
                document.getElementById('start').value = '';
                document.getElementById('end').value = '';

                myChart.config.data.datasets[0].data = deadlineDatasets;
                myChart.config.data.datasets[1].data = durationDatasets;

                myChart.config.options.scales.x.min = startDate;
                myChart.config.options.scales.x.max = endDate;

                myChart.update();
                document.getElementById('start').value = startDate.toISOString().split('T')[0];
                document.getElementById('end').value = endDate.toISOString().split('T')[0];
            }

            function timeFrame(period) {
                if (period.value == 'day' || period.value == 'week' || period.value == 'month' || period.value == 'year') {
                    myChart.config.options.scales.x.time.unit = period.value;
                    myChart.config.data.datasets[0].data = originalDatapoints;
                    myChart.config.data.datasets[1].data = originalDatapointss;
                    myChart.update();
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                const currentMonthElement = document.getElementById("currentMonth");

                const currentDate = new Date();

                const currentMonthYear = new Intl.DateTimeFormat('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                }).format(currentDate);
                currentMonthElement.innerText = currentMonthYear;

                const startInput = document.getElementById('start').value;
                const endInput = document.getElementById('end').value;

                if (startInput && endInput) {
                    const startDate = new Date(startInput);
                    const endDate = new Date(endInput);

                    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                        const monthNames = [
                            "January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"
                        ];

                        const startMonthYear = monthNames[startDate.getMonth()] + ' ' + startDate.getFullYear();
                        const endMonthYear = monthNames[endDate.getMonth()] + ' ' + endDate.getFullYear();

                        currentMonthElement.innerText = startMonthYear + ' - ' + endMonthYear;
                    } else {
                        console.error("Invalid date format");
                    }
                } else {
                    console.error("Start and end dates are required");
                }
            });

            function filteremployee() {
                const selectEmp = document.getElementById('assignnametime').value;

                const xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const filteredData = JSON.parse(this.responseText);
                        updateChart(filteredData);
                    }
                };
                xhttp.open("GET", "dbquery/filterempchart.php?employee_id=" + selectEmp, true);
                xhttp.send();
            }

            function updateChart(filteredData) {
                const formattedData = formatChartData(filteredData);

                myChart.data.datasets.forEach((dataset) => {
                    dataset.data = formattedData;
                });

                myChart.update();
            }

            function formatChartData(filteredData) {
                const formattedData = [];

                filteredData.forEach((dataItem) => {
                    formattedData.push({
                        x: [dataItem.deadline_start, dataItem.deadline_end],
                        y: `${dataItem.job}: ${dataItem.employee_nickname}`,
                        color: 'lightblue',
                        job: dataItem.job,
                        subjob: dataItem.subjob,
                        ticketstatus: dataItem.ticketstatus,
                        task: dataItem.task
                    });
                });

                return formattedData;
            }

            function clearSearchInputs() {
                document.getElementById('assignnametime').value = '';
                document.getElementById('start').value = '';
                document.getElementById('end').value = '';
                resetDate();
            }
        </script>
</body>

</html>