@extends('layout.main-layout')

@section('content')
    <!--<img src="{{ asset('public/' . $image) }}" width=100px;>-->
    {{-- <h3>Total Count : <span id="surveycount">{{ $pointCount }}<spam> --}}
    <label for="Polygon">Select added Feature:</label>
    <select id="addedFeature">
        <option value="none">Select an option</option>
        <option value="Polygon">Polygon</option>
        <option value="Merge">Merge</option>
        <option value="Delete">delete</option>
        <option value="LineString">Line</option>
    </select>
    <form id="mergeForm" class="d-none">
        <input type="text" name="firstmerge" id="firstmerge" required>
        <input type="text" name="secondmerge" id="secondmerge" required>
        <button type="submit" class="btn btn-primary">Merge</button>
    </form>
    <form id="delForm" class="d-none">
        <input type="text" name="delete_gis_id" id="delete_gis_id" required>
        <button type="submit" class="btn btn-primary">Delete</button>
    </form>
    <div id="map"></div>
    <div class="modal fade" id="buildingModal" tabindex="-1" aria-labelledby="buildingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buildingModalLabel">Feature Properties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="building_img" alt="" width="300px">
                    <h4>Feature Properties</h4>
                    <ul id="featurePropertiesList">
                        <!-- Feature properties will be displayed here -->
                    </ul>
                    <hr>
                    <h4>Feature Form</h4>

                    <form id="buildingForm" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">
                            <div id="alertBox" class="alert alert-danger" style="display: none;">
                            </div>
                            <div class="form-group">
                                <label for="gis">Gis</label>
                                <input type="text" class="form-control" id="gisIdInput" name="gisid" readonly>
                            </div>
                            <div class="form-group">
                                <label for="number_bill">Number_of_Bill</label>
                                <input type="text" name="number_bill" class="form-control" id="number_bill">
                                <div id="number_bill_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="number_shop">Number_of_Shop</label>
                                <input type="text" name="number_shop" class="form-control" id="number_shop">
                                <div id="number_shop_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="number_floor">Maximum_Floor</label>
                                <input type="text" name="number_floor" class="form-control" id="number_floor">
                                <div id="number_floor_error"class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="Percentage">Percentage</label>
                                <select name="percentage" id="percentage" class="form-control">
                                    <option value=""></option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                    <option value="50">50</option>
                                    <option value="60">60</option>
                                    <option value="70">70</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                    <option value="90">90</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div id="percentage_error"class="error-message"></div>


                            <div class="form-group">
                                <label for="building_name">Building_name</label>
                                <input type="text" name="building_name" class="form-control" id="building_name">
                                <div id="building_name_error " class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="Building_usage">Building_usage</label>
                                <select name="building_usage" id="building_usage" class="form-control">
                                    <option value=""></option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Mixed">Mixed</option>
                                    <option value="Vacant Land">Vacant Land</option>
                                    <option value="Kalyanamandapam">Kalyanamandapam</option>
                                    <option value="OFFICE / LODGE / THEATER / RESTUARANTS">OFFICE / LODGE / THEATER /
                                        RESTUARANTS</option>
                                    <option value="Others">Others</option>
                                    <option value="Underconstruction">Underconstruction</option>
                                </select>
                                <div id="building_usage_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="Building_type">Building_type</label>
                                <select name="building_type" id="building_type" class="form-control">
                                    <option value=""></option>
                                    <option value="Indepentend">Indepentend</option>
                                    <option value="Flat">Flat</option>
                                    <option value="Flat-Multistoried">Flat-Multistoried</option>
                                    <option value="Kalyanamandapam">Kalyanamandapam</option>
                                    <option value="Hotel/Cinema Theatre">Hotel/Cinema Theatre</option>
                                    <option value="Central Government">Central Government Building</option>
                                    <option value="State Government">State Government Building</option>
                                    <option value="Municipality/Corporation">Municipality/Corporation</option>
                                    <option value="Vacant Land">Vacant Land</option>
                                    <option value="Education Institute">Education Institution</option>
                                    <option value="temple">Temple</option>
                                    <option value="Others">Others</option>
                                    <option value="Amma unavagam">Amma unavagam</option>
                                    <option value="Public Toilet">Public Toilet</option>
                                    <option value="Underconstruction">Underconstruction</option>
                                </select>
                                <div id="building_type_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="construction_type">Construction_type</label>
                                <select name="construction_type" id="construction_type" class="form-control">
                                    <option value=""></option>
                                    <option value="PERMANENT">PERMANENT</option>
                                    <option value="SEMI-PERMANENT">SEMI-PERMANENT</option>
                                    <option value="VACAND LAND">VACAND LAND</option>
                                </select>
                                <div id="construction_type_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="road_name">Road Name</label>
                                <select class="form-control" name="road_name" id="road_name">
                                    <option value="">Select a road</option>
                                    @php
                                        $uniqueRoadNames = $mis->unique('road_name');
                                    @endphp
                                    @foreach ($uniqueRoadNames as $item)
                                        <option value="{{ $item->road_name }}">{{ $item->road_name }}</option>
                                    @endforeach
                                </select>
                                <div id="road_name_error" class="error-message"></div>
                            </div>





                            <div class="form-group">
                                <label for="new_address_select">New Address</label>
                                <input type="text" name="new_address" id="new_address" class="form-control">

                                <div id="new_address_div" class="error-message"></div>
                            </div>

                            <div class="form-group">
                                <label for="ugd">UGD</label>
                                <input type="text" name="ugd" class="form-control" id="ugd">
                                <div id="ugd_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="basement">Basement</label>
                                <input type="text" name="basement" class="form-control" id="basement">
                                <div id="basement_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="rainwater_harvesting">Rainwater_harvesting</label>
                                <select name="rainwater_harvesting" id="rainwater_harvesting" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="rainwater_harvesting_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="parking">Parking</label>
                                <select name="parking" id="parking" class="form-control">
                                    <option value=""></option>
                                    <option value="NO">NO</option>
                                    <option value="Basement">Basement</option>
                                    <option value="Ground-Parking">Ground-Parking</option>
                                </select>
                                <div id="parking_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="ramp">Ramp</label>
                                <select name="ramp" id="ramp" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="ramp_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="hoarding">Hoarding</label>
                                <select name="hoarding" id="hoarding" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="hoarding_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="cell_tower">Cell_tower</label>
                                <select name="cell_tower" id="cell_tower" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="cell_tower_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="overhead_tank">OverHead Tank</label>
                                <select name="overhead_tank" id="overhead_tank" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="overhead_tank_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="liftroom">LiftRoom</label>
                                <select name="liftroom" id="liftroom" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="liftroom_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="headroom">Head Room</label>
                                <select name="headroom" id="headroom" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="headroom_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="cctv">CCTV</label>
                                <select name="cctv" id="cctv" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="cctv_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="solar_panel">Solar_panel</label>
                                <select name="solar_panel" id="solar_panel" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                                <div id="solar_panel_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="water_connection">Water_connection</label>
                                <select name="water_connection" id="water_connection" class="form-control">
                                    <option value="NO">NO</option>
                                    <option value="Bore">Bore</option>
                                    <option value="OPEN-WELL">OPEN-WELL</option>
                                    <option value="Bore-WELL">Bore-WELL AND METRO</option>
                                    <option value="METRO">METRO</option>
                                    <option value="No Connection">No Connection</option>
                                </select>
                                <div id="water_connection_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="phone">phone_numnber</label>
                                <input type="text" name="phone" id="phone" class="form-control"
                                    value="0123456789">

                                <div id="phone_error" class="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="remarks">remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control"
                                    value="0123456789">

                                <div id="remarks_error" class=" error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="value">Picture</label>
                                <input type="file" name="image" id="image" class="form-control">
                                <div id="image_error" class="error-message"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                aria-label="Close">Close</button>


                            <button type="submit" id="buildingsubmitBtn" class="btn btn-primary">Save</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pointModal" tabindex="-1" aria-labelledby="pointModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pointModalLabel">Bill Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                    <form method="POST" enctype="multipart/form-data" id="pointForm">
                        @csrf
                        <div class="modal-body">
                        </div>
                        <div class="form-group">
                            <label for="Assessment Type">Assessment Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="OLD">OLD</option>
                                <option value="NEW">NEW</option>
                                <option value="OTHER">OTHER WARD</option>
                            </select>
                        </div>
                        <div id="suveyedbtn"></div>

                        <div class="form-group d">
                            <label for="gis">Gis</label>
                            <input type="text" class="form-control" id="pointgis" name="point_gisid" readonly>
                            <div id="point_gisid_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="assessment">Assessment_no</label>
                            <input type="text" name="assessment" class="form-control" id="assessment">
                            <div id="assessment_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="old_assessment">Old Assessment</label>
                            <input type="text" name="old_assessment" class="form-control" id="old_assessment">
                            <div id="old_assessment_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="owner_name">Owner Name</label>
                            <input type="text" name="owner_name" class="form-control" id="owner_name">
                            <div id="owner_name_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="present_owner_name">Present Owner Name</label>
                            <input type="text" name="present_owner_name" class="form-control"
                                id="present_owner_name">
                            <div id="present_owner_name_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="floor"> Floor</label>
                            <input type="text" name="floor" class="form-control" id="floor">
                            <div id="floor_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="old_door_no"> Old_door_no</label>
                            <input type="text" name="old_door_no" class="form-control" id="old_door_no">
                            <div id="old_door_no_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="new_door_no"> New_door_no</label>
                            <input type="text" name="new_door_no" class="form-control" id="new_door_no">
                            <div id="new_door_no_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="bill_usage">Bill_usage</label>
                            <select name="bill_usage" id="bill_usage" class="form-control">
                                <option value=""></option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Mixed">Mixed</option>
                            </select>
                            <div id="bill_usage_error" class=" error-message"></div>
                        </div>
                        {{-- <div class="form-group">
                            <label for="eb">Eb_number</label>
                            <input type="text" name="eb" class="form-control" id="eb">
                            <div id="eb_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="water_tax">water_tax</label>
                            <input type="text" name="water_tax" class="form-control" id="water_tax">
                            <div id="water_tax_error" class=" error-message"></div>
                        </div> --}}

                        <div class="form-group">
                            <label for="phone">phone_numnber</label>
                            <input type="text" name="phone_number" class="form-control" id="phone">
                            <div id="phone_number_error" class=" error-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" class="form-control" id="remarks">
                            <div id="remarks_error" cls></div>
                        </div>
                        <div id="append"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>

                    <button type="submit" id="pointSubmit" class="btn btn-primary">Submit</button>


                </div>
                </form>

            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="lineModal" tabindex="-1" aria-labelledby="lineModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lineModalLabel">Feature Properties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Feature Properties</h4>
                    <ul id="featureline">
                        <!-- Feature properties will be displayed here -->
                    </ul>
                    <hr>
                    <form method="POST" enctype="multipart/form-data" id="lineForm">
                        @csrf
                        <div class="modal-body">
                        </div>
                        <input type="text" name="linegisid" id="linegisid" class="form-control">
                        <input type="text" name="roadname" id="roadname" class="form-control">
                        <button type="submit" id="pointSubmit" class="btn btn-primary">Submit</button>

                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>

                </div>
            </div>
        </div>
    </div>
    <script>
        function showFlashMessage(message, type) {
            let flashId =
                type === "success" ?
                "#flash-message-success" :
                "#flash-message-error";
            let flashContentId =
                type === "success" ?
                "#flash-message-success-content" :
                "#flash-message-error-content";

            // Clear previous messages
            $(flashContentId).text(message);

            // Fade in the flash message
            $(flashId).fadeIn();

            // Auto-hide the message after 3 seconds
            setTimeout(function() {
                $(flashId).fadeOut();
            }, 3000);
        }
        // Initialize the extent data from the Laravel controller
        var extentData = @json($extensions); // Laravel variable passed from the controller
        var imagepath = "{{ asset($image) }}"; // Path to the image asset
        let polygons = @json($polygons); // Polygons data from Laravel
        let points = @json($points); // Points data from Laravel
        let lines = @json($lines); // Lines data from Laravel
        let polygonDatas = @json($polygonDatas); // Additional polygon data
        let data = @json($data); // Other data
        let pointDatas = @json($pointDatas); // Additional point data
        let mis = @json($mis); // Miscellaneous data (e.g., assessment data)
        const routes = {
            surveyorPointDataUpload: "{{ route('surveyor.pointdata-upload') }}",
            surveyorPolygonDatasUpload: "{{ route('surveyor.polygondata-upload') }}",
            surveyorDelete: "{{ route('admin.surveyorDelete', ['id' => 'mm']) }}", // Use a placeholder
            addPolygonFeature: "{{ route('surveyor.addPolygonFeature') }}",
            addLineFeature: "{{ route('surveyor.addLineFeature') }}",

            mergePolygon: "{{ route('surveyor.mergePolygon') }}",
            deletePolygon: "{{ route('surveyor.deletePolygon') }}",
            updateRoadName: "{{ route('surveyor.updateRoadName') }}",
        };
        // Parse extentData and create a float array
    </script>
@endsection
