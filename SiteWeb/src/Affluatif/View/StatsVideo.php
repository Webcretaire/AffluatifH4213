<?php

namespace Affluatif\View;

use Affluatif\Processing\VideoProcessing;
use Affluatif\Services\Functions;
use Affluatif\Services\Securite;

/**
 * Class StatsVideo
 *
 * @package Affluatif\View
 */
class StatsVideo extends BaseTemplate
{
    private $flux;

    public function __construct(\PDO $bdd = null)
    {
        parent::__construct($bdd);

        $this->services->getSecurite()->verificationFlux(Securite::validateInt($_GET['f']));

        $this->flux = $this->bddRequest(
            "SELECT * FROM flux_video WHERE id = :id",
            ['id' => $_GET['f']]
        )->fetch();

        list($this->flux['date_max_affluence'], $this->flux['max_affluence']) = $this->bddRequest(
            'SELECT date, nombre
            FROM affluence_flux 
            INNER JOIN flux_video ON flux_video.id = affluence_flux.flux_id 
            WHERE nombre = 
                (SELECT MAX(nombre) AS maxFlux  
                FROM affluence_flux 
                INNER JOIN flux_video ON affluence_flux.flux_id = flux_video.id 
                WHERE flux_id = :id
                GROUP BY affluence_flux.flux_id) 
            AND affluence_flux.flux_id= :id
            ORDER BY date DESC',
            ['id' => $this->flux['id']]
        )->fetch();
    }

    protected function blockHead()
    {
        parent::blockHead();
        ?>
        <script type="text/javascript" src="/js/chartist.min.js"></script>
        <link href="/css/chartist.min.css" rel="stylesheet"/>
        <script type="text/javascript" src="/js/moment-locales.js"></script>
        <script type="text/javascript" src="/js/bootstrap-datetimepicker.min.js"></script>
        <link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet"/>
        <?php
    }

    protected function blockBanner()
    {
        ?>
        <h1 class="text-white black-glow"><?php echo $this->flux['description']; ?></h1>
        <button class="btn btn-primary mt-10 btn-lg"
                onclick="loadModale('/m/alertes?f=<?php echo $this->flux['id']; ?>' , 'modale__alertes', updateDatetime)">
            Alertes
        </button>
        <?php
    }

    protected function blockSections()
    {
        ?>
        <section class="about-generic-area">
            <div class="container pb-30 pt-20">

                <!-- Live -->

                <h2 class="mt-20">
                    <a data-toggle="collapse" href="#collapse_live" role="button" aria-expanded="false">
                        <span class="fa fa-arrow-circle-right"></span> Visualisation en direct
                    </a>
                </h2>

                <hr/>

                <div class="mt-40 mb-20 collapse text-center" id="collapse_live">
                    <i class="loading_gear fa fa-cog fa-spin fa-5x" aria-hidden="true"></i>
                </div>

                <!-- Maps -->

                <h2 class="mt-20">
                    <a data-toggle="collapse" href="#collapse_maps" role="button" aria-expanded="false">
                        <span class="fa fa-arrow-circle-right"></span> Localisation
                    </a>
                </h2>

                <hr/>

                <div class="mt-40 mb-20 collapse text-center" id="collapse_maps">
                    <div id="map" style="height: 400px; width: 100%"></div>
                </div>

                <!-- Record affluence -->

                <h2 class="mt-20">
                    <a data-toggle="collapse" href="#collapse_record" role="button" aria-expanded="false">
                        <span class="fa fa-arrow-circle-right"></span> Dernier record d'affluence
                    </a>
                </h2>

                <hr/>

                <div class="mb-40 collapse text-center" id="collapse_record">
                    <h3 class="mb-20">
                        <small>
                            <?php echo ucfirst(Functions::dateToFrench($this->flux['date_max_affluence'])) . ' : ' . $this->flux['max_affluence']; ?>
                            personnes
                        </small>
                    </h3>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($this->flux['image_max_affluence']); ?>"
                         alt="Pic d'affluence"
                         style="max-width: 75%"/>
                </div>

                <!-- Historique affluence -->

                <h2 class="mt-20">
                    <a data-toggle="collapse" href="#collapse_affluence" role="button" aria-expanded="false">
                        <span class="fa fa-arrow-circle-right"></span>
                        Affluence au cours du temps
                        <small>(Pic d'affluence maximal)</small>
                    </a>
                </h2>

                <hr/>


                <div class="mb-40 collapse text-center" id="collapse_affluence">
                    <div class="col-sm-6 offset-sm-3">
                        <div class="input-group-icon mt-10 mb-10">
                            <div class="icon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
                            <div class="form-select" id="default-select">
                                <select id="delay-values">
                                    <option value="heure">Dernière heure</option>
                                    <option value="jour" selected>24 dernières heures</option>
                                    <option value="semaine">7 derniers jours</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="chart" style="height: 400px;"></div>
                    <p>
                        Légende :
                        <?php
                        $classes = $this->bddRequest(
                            'SELECT DISTINCT classe FROM classe_flux WHERE flux_id = :flux ORDER BY classe',
                            ['flux' => $_GET['f']]
                        );

                        $i = 0;
                        $colors = ['#35bcf8', '#82f827', '#9900cc'];
                        foreach ($classes as $classe) {
                            ?>
                            <span style="color: <?php echo $colors[$i++]; ?>">
                                <?php echo array_search($classe['classe'], VideoProcessing::$classes); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </p>
                </div>

                <!-- Prévision affluence -->

                <h2 class="mt-20">
                    <a data-toggle="collapse" href="#collapse_prevision" role="button" aria-expanded="false">
                        <span class="fa fa-arrow-circle-right"></span>
                        Prévision d'affluence
                        <small>(Personnes uniquement)</small>
                    </a>
                </h2>

                <hr/>

                <div class="mb-40 collapse text-center" id="collapse_prevision">
                    <div class="col-sm-6 offset-sm-3">
                        <div class="input-group-icon mt-10 mb-10 just">
                            <div class="icon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
                            <div class="form-select" id="default-select">
                                <select id="delay-values-prediction">
                                    <option value="heure">3 prochaines heures</option>
                                    <option value="jour" selected>24 prochaines heures</option>
                                    <option value="semaine">7 prochains jours</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="plotly">
                        <i class="loading_gear fa fa-cog fa-spin fa-5x" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Generic Start -->
        <?php
    }

    protected function blockJavascript()
    {
        parent::blockJavascript();
        $dataHours = [];
        foreach (Functions::last(24, 'hours', 'Y-m-d H') as $date) {
            $dataHours[] = (int)$this->bddRequest(
                "SELECT MAX(nombre) FROM affluence_flux WHERE flux_id = :id AND date LIKE '$date%'",
                ['id' => $this->flux['id']]
            )->fetchColumn();
        }
        $dataDays = [];
        foreach (Functions::last(7, 'days', 'Y-m-d') as $date) {
            $dataDays[] = (int)$this->bddRequest(
                "SELECT MAX(nombre) FROM affluence_flux WHERE flux_id = :id AND date LIKE '$date%'",
                ['id' => $this->flux['id']]
            )->fetchColumn();
        }
        ?>
        <script type="text/javascript">
            function updateDatetime() {
                let picker1 = $('#datetimepicker1');
                let picker2 = $('#datetimepicker2');

                picker1.datetimepicker({
                    locale: 'fr',
                    format: 'HH:mm:ss',
                    icons: {
                        up: "fa fa-chevron-circle-up",
                        down: "fa fa-chevron-circle-down"
                    }
                });

                picker2.datetimepicker({
                    locale: 'fr',
                    format: 'HH:mm:ss',
                    useCurrent: false,
                    icons: {
                        up: "fa fa-chevron-circle-up",
                        down: "fa fa-chevron-circle-down"
                    }
                });
            }

            var latLng = {lat: <?php echo $this->flux['loc_lat']; ?>, lng: <?php echo $this->flux['loc_lon']; ?>};

            var map;

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: latLng,
                    zoom: 14
                });

                new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: 'Caméra'
                });
            }

            var predictionFetched = false;

            $('#delay-values-prediction').change(function () {
                fetchPrevisions(true);
            });

            function fetchPrevisions(forceUpdate = false) {
                if (!predictionFetched || forceUpdate) {
                    $('#plotly').html('<i class="loading_gear fa fa-cog fa-spin fa-5x" aria-hidden="true"></i>');
                    let tu = 42;
                    let d = 24;
                    switch ($('#delay-values-prediction').val()) {
                        case 'heure':
                            d = 3;
                            break;
                        case 'jour':
                            d = 24;
                            break;
                        case 'semaine':
                            d = 7 * 24;
                            break;
                    }
                    $.get('/p/prediction-flux?f=<?php echo $this->flux['id']; ?>&tu=' + tu + '&d=' + d, function (data) {
                        $('#plotly').html('<iframe src="' + data + '" style="width: 100%; height: 400px"></iframe>');
                    });
                    predictionFetched = true;
                }
            }

            $('#collapse_prevision').on('show.bs.collapse', fetchPrevisions);

            var fetchdelay = 60;
            var timer;
            var chart;

            function fetchChartData() {
                clearTimeout(timer);
                $.get(
                    '/p/historique-affluence?f=<?php echo $this->flux['id']; ?>&d=' + encodeURIComponent($('#delay-values').val()),
                    function (data) {
                        if (chart)
                            chart.detach();
                        chart = new Chartist.Line('#chart', {
                            labels: data.labels,
                            series: data.series
                        }, {
                            fullWidth: true,
                            chartPadding: {
                                right: 40
                            }
                        });
                        $('#chart').html(' ');
                    }
                );
                timer = setTimeout(fetchChartData, fetchdelay * 1000);
            }

            $('#delay-values').change(fetchChartData);

            $('#collapse_affluence').on('show.bs.collapse', fetchChartData);

            $('#collapse_live').on('show.bs.collapse', function () {
                $('#collapse_live').html(
                    '<img src="<?php echo $this->flux['actif'] ? $this->flux['url'] : '/img/offline.jpg'; ?>"\n' +
                    '     alt="Prévisualisation vidéo"\n' +
                    '     style="max-width: 75%"/>'
                );
            });
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php
        echo $this->services->getConfig()->getGoogleApiMaps();
        ?>&callback=initMap"
                async defer></script>
        <?php
    }
}