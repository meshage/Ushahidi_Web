<!-- main body -->
<div id="main" class="clearingfix">
    <div id="mainmiddle" class="floatbox withright">

    <?php if($site_message != '') { ?>
        <div class="green-box">
            <h3><?php echo $site_message; ?></h3>
        </div>
    <?php } ?>

    <div id="threesteps">
        <div>
            <h2>Step 1: Where are you?</h2>
            <p>Locating where you are on the map helps identify what reports are most relevant to you.</p>
            <form id="location_search" action="">
                <input type="text" value="I am in..." onfocus="$(this).val() == 'I am in...' ? $(this).val('') : true;" onblur="$(this).val() == '' ? $(this).val('I am in...') : true;"/>
                <a href="#">Go</a>
            </form>
        </div>

        <div>
            <h2>Step 2: Submit a report</h2>
            <p>Submitting a report helps others identify hazards/solutions in your area.</p>
            <!-- submit incident -->
            <div class="submit-incident"><a href="<?php echo url::site() . 'reports/submit/'; ?>">Submit a Report</a></div>
            <!-- / submit incident -->
        </div>

        <div>
            <h2>Request help</h2>
            <p>Clicking on this button requests help from the Student Volunteer Army.</p>
            <div class="submit-incident"><a href="<?php echo url::site() . 'volunteer-army'; ?>">Request help</a></div>
        </div>

        <script type="text/javascript">
            function zoomMapToSearch() {
                $.ajax({
                        url: 'geocode/json',
                        data: {
                            address: $('#location_search input').val()
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == "OK") {
                                externalZeroIn(data.lon, data.lat, 16);
                            }
                        }
                });
                return false;
            }

            jQuery(window).load(function() {
                $('#location_search input').bind('change', zoomMapToSearch);
                $('#location_search a').bind('click', zoomMapToSearch);
                $('#location_search').bind('submit', function() { return false; });
            });
        </script>
        </div>

        <!-- right column -->
        <div id="right" class="clearingfix">

            <!-- category filters -->
            <div class="cat-filters clearingfix">
                <strong><?php echo Kohana::lang('ui_main.category_filter');?> <span>[<a href="javascript:toggleLayer('category_switch_link', 'category_switch')" id="category_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
            </div>

            <ul id="category_switch" class="category-filters">
                <?php $normalized_all_categories = str_replace(' ', '-', strtolower(Kohana::lang('ui_main.all_categories')))?>
                <li><a class="active" id="cat_0" href="#<?php echo $normalized_all_categories ?>" data-name="<?php echo $normalized_all_categories ?>"><span class="swatch" style="background-color:<?php echo "#".$default_map_all;?>"></span><span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span></a></li>
                <?php
                    foreach ($categories as $category => $category_info)
                    {
                        $category_title = $category_info[0];
                        $category_color = $category_info[1];
                        $category_image = '';
                        $category_normalized_name = str_replace(' ', '-', strtolower($category_title));
                        $color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
                        if($category_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$category_info[2])) {
                            $category_image = html::image(array(
                                'src'=>Kohana::config('upload.relative_directory').'/'.$category_info[2],
                                'style'=>'float:left;padding-right:5px;'
                                ));
                            $color_css = '';
                        }
                        echo '<li><a href="#' . $category_normalized_name . '" id="cat_'. $category .'" data-name="' . $category_normalized_name . '"><span '.$color_css.'>'.$category_image.'</span><span class="category-title">'.$category_title.'</span></a>';
                        // Get Children
                        echo '<div class="hide" id="child_'. $category .'">';
                                                if( sizeof($category_info[3]) != 0)
                                                {
                                                    echo '<ul>';
                                                    foreach ($category_info[3] as $child => $child_info)
                                                    {
                                                            $child_title = $child_info[0];
                                                            $child_color = $child_info[1];
                                                            $child_image = '';
                                                            $child_normalized_name = str_replace(' ', '-', strtolower($child_title));
                                                            $color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
                                                            if($child_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$child_info[2])) {
                                                                    $child_image = html::image(array(
                                                                            'src'=>Kohana::config('upload.relative_directory').'/'.$child_info[2],
                                                                            'style'=>'float:left;padding-right:5px;'
                                                                            ));
                                                                    $color_css = '';
                                                            }
                                                            echo '<li style="padding-left:20px;"><a href="#' . $child_normalized_name . '" id="cat_'. $child .'" data-name="' . $child_normalized_name . '"><span '.$color_css.'>'.$child_image.'</span><span class="category-title">'.$child_title.'</span></a></li>';
                                                    }
                                                    echo '</ul>';
                                                }
                        echo '</div></li>';
                    }
                ?>
            </ul>
            <!-- / category filters -->

            <?php
            if ($layers)
            {
                ?>
                <!-- Layers (KML/KMZ) -->
                <div class="cat-filters clearingfix" style="margin-top:20px;">
                    <strong><?php echo Kohana::lang('ui_main.layers_filter');?> <span>[<a href="javascript:toggleLayer('kml_switch_link', 'kml_switch')" id="kml_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
                </div>
                <ul id="kml_switch" class="category-filters">
                    <?php
                    foreach ($layers as $layer => $layer_info)
                    {
                        $layer_name = $layer_info[0];
                        $layer_color = $layer_info[1];
                        $layer_url = $layer_info[2];
                        $layer_file = $layer_info[3];
                        $layer_link = (!$layer_url) ?
                            url::base().Kohana::config('upload.relative_directory').'/'.$layer_file :
                            $layer_url;
                        echo '<li><a href="#" id="layer_'. $layer .'"
                        onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
                        <div>'.$layer_name.'</div></a></li>';
                    }
                    ?>
                </ul>
                <!-- /Layers -->
                <?php
            }
            ?>


            <?php
            if ($shares)
            {
                ?>
                <!-- Layers (Other Ushahidi Layers) -->
                <div class="cat-filters clearingfix" style="margin-top:20px;">
                    <strong><?php echo Kohana::lang('ui_main.other_ushahidi_instances');?> <span>[<a href="javascript:toggleLayer('sharing_switch_link', 'sharing_switch')" id="sharing_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
                </div>
                <ul id="sharing_switch" class="category-filters">
                    <?php
                    foreach ($shares as $share => $share_info)
                    {
                        $sharing_name = $share_info[0];
                        $sharing_color = $share_info[1];
                        echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
                        <div>'.$sharing_name.'</div></a></li>';
                    }
                    ?>
                </ul>
                <!-- /Layers -->
                <?php
            }
            ?>

            <?php
            // Action::main_sidebar - Add Items to the Entry Page Sidebar
            Event::run('ushahidi_action.main_sidebar');
            ?>

        </div>
        <!-- / right column -->

        <!-- content column -->
        <div id="content" class="clearingfix">
            <div class="floatbox">
                <h2>Click on map icons to see local reports</h2>
                <div class="fullscreenmap-btn">
                    <a href="#" class="fullscreenmap_click">Full Screen Map</a>
                </div>
                <?php
                // Map and Timeline Blocks
                echo $div_map;
                echo $div_timeline;
                ?>
            </div>
        </div>
        <!-- / content column -->

    </div>
</div>
<!-- / main body -->

<!-- content -->
<div class="content-container">

    <!-- content blocks -->
    <div class="content-blocks clearingfix">

        <!-- left content block -->
        <div class="content-block-left">
            <div class="content-block-left-article">
            <h5><?php echo Kohana::lang('ui_main.reports'); ?></h5>
            <table class="table-list">
                <thead>
                    <tr>
                        <th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
                        <th scope="col" class="location"><?php echo Kohana::lang('ui_main.location'); ?></th>
                        <th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($total_items == 0)
                    {
                    ?>
                    <tr><td colspan="3"><?php echo Kohana::lang('ui_main.no_reports'); ?></td></tr>

                    <?php
                    }
                    foreach ($incidents as $incident)
                    {
                        $incident_id = $incident->id;
                        $incident_title = text::limit_chars($incident->incident_title, 40, '...', True);
                        $incident_date = $incident->incident_date;
                        $incident_date = date('H:i M d, Y', strtotime($incident->incident_date));
                        $incident_location = $incident->location->location_name;
                    ?>
                    <tr>
                        <td><a href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>"> <?php echo $incident_title ?></a></td>
                        <td><?php echo $incident_location ?></td>
                        <td><?php echo $incident_date; ?></td>
                    </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
            <a class="more" href="<?php echo url::site() . 'reports/' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a>
            </div>

            <div class="clearingfix"></div>

            <div class="content-block-left-article">
            <h5><?php echo Kohana::lang('ui_main.official_news'); ?></h5>
            <table class="table-list">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Kohana::lang('ui_main.title'); ?></th>
                        <th scope="col"><?php echo Kohana::lang('ui_main.source'); ?></th>
                        <th scope="col"><?php echo Kohana::lang('ui_main.date'); ?></th>
                    </tr>
                </thead>
                    <?php
                                        if ($feeds->count() != 0)
                                        {
                                            echo '<tbody>';
                                            foreach ($feeds as $feed)
                                            {
                                                    $feed_id = $feed->id;
                                                    $feed_title = text::limit_chars($feed->item_title, 40, '...', True);
                                                    $feed_link = $feed->item_link;
                                                    $feed_date = date('H:i M d, Y', strtotime($feed->item_date));
                                                    $feed_source = text::limit_chars($feed->feed->feed_name, 15, "...");
                                            ?>
                                            <tr>
                                                    <td><a href="<?php echo $feed_link; ?>" target="_blank"><?php echo $feed_title ?></a></td>
                                                    <td><?php echo $feed_source; ?></td>
                                                    <td><?php echo $feed_date; ?></td>
                                            </tr>
                                            <?php
                                            }
                                            echo '</tbody>';
                                        }
                                        else
                                        {
                                            echo '<tbody><tr><td></td><td></td><td></td></tr></tbody>';
                                        }
                    ?>
            </table>
            <a class="more" href="<?php echo url::site() . 'feeds' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a>
            </div>
        </div>
        <!-- / left content block -->

        <!-- right content block -->
        <div class="content-block-right">
            <!-- additional content -->
            <?php
            if (Kohana::config('settings.allow_reports'))
            {
                ?>
                <div class="additional-content">

                    <div class="howto-report">
                        <h5><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5>
                    <ol>
                        <?php if (!empty($phone_array))
                        { ?><li><?php echo Kohana::lang('ui_main.report_option_1')." "; ?> <?php foreach ($phone_array as $phone) {
                            echo "<strong>". $phone ."</strong>";
                            if ($phone != end($phone_array)) {
                                echo " or ";
                            }
                        } ?></li><?php } ?>
                        <?php if (!empty($report_email))
                        { ?><li><?php echo Kohana::lang('ui_main.report_option_2')." "; ?> <a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></li><?php } ?>
                        <?php if (!empty($twitter_hashtag_array))
                                    { ?><li><?php echo Kohana::lang('ui_main.report_option_3')." "; ?> <?php foreach ($twitter_hashtag_array as $twitter_hashtag) {
                        echo "<strong>". $twitter_hashtag ."</strong>";
                        if ($twitter_hashtag != end($twitter_hashtag_array)) {
                            echo " or ";
                        }
                        } ?></li><?php
                        } ?><li><a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a></li>
                    </ol>
                    <!-- submit incident -->
                    <div class="submit-incident"><a href="<?php echo url::site() . 'reports/submit/'; ?>">Submit a Report</a></div>
                    <!-- / submit incident -->
</div>


                <div class="social-buttons">
                    <h5>Follow developments on</h5>
                    <ul>
                        <li><a href="#"><img src="/themes/ccnz/images/twitter.png" alt="Twitter logo"><br><a href="#">Twitter</a></li>
                        <li><a href="#"><img src="/themes/ccnz/images/facebook.png" alt="Facebook logo"><br><a href="#">Facebook</a></li>
                    </ul>

                </div>
                </div>
            <?php } ?>
            <!-- / additional content -->

        </div>
        <!-- / right content block -->

    </div>
    <!-- /content blocks -->

</div>
<!-- content -->
