<?php

namespace OXI_FLIP_BOX_PLUGINS\Public_Render;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Style_1
 * Content of Flipbox Plugins Plugins
 *
 * @author $biplob018
 */

/**
 * Description of Create
 *
 * @author biplo
 */
use OXI_FLIP_BOX_PLUGINS\Page\Public_Render;

class Style11 extends Public_Render {

    public function default_render() {
        $styleid = $this->oxiid;
        $styledata = explode('|', $this->dbdata['css']);
        $listdata = $this->child;
        ?>
        <div class="oxilab-flip-box-wrapper">
            <?php
            foreach ($listdata as $value) {
                if (!empty($value['files'])):
                    $filesdata = explode("{#}|{#}", $value['files']);
                    ?>
                    <div class="<?php echo $styledata[43]; ?> oxilab-flip-box-padding-<?php echo $styleid; ?>"
                         sa-data-animation="<?php echo $styledata[55]; ?>"
                         sa-data-animation-offset="100%"
                         sa-data-animation-delay="0ms"
                         sa-data-animation-duration=" <?php echo ($styledata[57] * 1000); ?>ms">
                        <div class="<?php echo ($this->admin == 'admin') ? 'oxilab-ab-id' : ''; ?> oxilab-flip-box-body-<?php echo $styleid; ?> oxilab-flip-box-body-<?php echo $styleid; ?>-<?php echo $value['id']; ?>">
                            <?php
                            if ($filesdata[13] == '' && $filesdata[15] != '') {
                                echo '<a href="' . $filesdata[15] . '" target="' . $styledata[53] . '">';
                                $fileslinkend = '</a>';
                            } else {
                                $fileslinkend = '';
                            }
                            ?>
                            <div class="oxilab-flip-box-body-absulote">
                                <div class="<?php echo $styledata[1]; ?>">
                                    <div class="oxilab-flip-box-style-data <?php echo $styledata[3]; ?>">
                                        <div class="oxilab-flip-box-style">
                                            <div class="oxilab-flip-box-front">
                                                <div class="oxilab-flip-box-<?php echo $styleid; ?>">
                                                    <div class="oxilab-flip-box-<?php echo $styleid; ?>-data">                                            
                                                        <div class="oxilab-icon">
                                                            <div class="oxilab-icon-data">
                                                                <?php echo $this->font_awesome_render($filesdata[3]) ?>
                                                            </div>
                                                        </div>
                                                        <div class="oxilab-heading">
                                                            <?php echo $this->text_render($filesdata[1]); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="oxilab-flip-box-back">
                                                <div class="oxilab-flip-box-back-<?php echo $styleid; ?>">
                                                    <div class="oxilab-flip-box-back-<?php echo $styleid; ?>-data">
                                                        <div class="oxilab-heading">
                                                            <?php echo $this->text_render($filesdata[7]); ?>
                                                        </div>
                                                        <div class="oxilab-icon">
                                                            <div class="oxilab-icon-data">
                                                                <?php echo $this->font_awesome_render($filesdata[11]) ?>
                                                            </div>
                                                        </div>
                                                        <div class="oxilab-info">
                                                            <?php echo $this->text_render($filesdata[9]); ?>
                                                        </div>
                                                        <?php
                                                        if ($filesdata[13] != '') {
                                                            echo '<a href="' . $filesdata[15] . '" target="' . $styledata[53] . '">';
                                                            echo '<div class="oxilab-button">
                                                                    <div class="oxilab-button-data">
                                                                    ' . $this->text_render($filesdata[13]) . '
                                                                    </div>
                                                                </div>';
                                                            echo '</a>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo $fileslinkend;
                            echo $this->admin_edit_panel($value['id']);
                            ?>
                        </div>

                        <style>
                <?php
                if ($filesdata[5] != '') {
                    echo '.oxilab-flip-box-body-' . $styleid . '-' . $value['id'] . ' .oxilab-flip-box-' . $styleid . '{
background: linear-gradient(' . $styledata[5] . ', ' . $styledata[5] . '), url("' . $filesdata[5] . '");
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;
}';
                }
                if ($filesdata[17] != '') {
                    echo '.oxilab-flip-box-body-' . $styleid . '-' . $value['id'] . ' .oxilab-flip-box-back-' . $styleid . '{
background: linear-gradient(' . $styledata[13] . ', ' . $styledata[13] . '), url("' . $filesdata[17] . '");
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;
}';
                }
                ?>
                        </style>
                    </div>
                    <?php
                endif;
            }
            ?>

            <style>
                .oxilab-flip-box-padding-<?php echo $styleid; ?>{
                    padding: <?php echo $styledata[49]; ?>px <?php echo $styledata[51]; ?>px;
                    -webkit-transition:  opacity <?php echo $styledata[57]; ?>s linear;
                    -moz-transition:  opacity <?php echo $styledata[57]; ?>s linear;
                    -ms-transition:  opacity <?php echo $styledata[57]; ?>s linear;
                    -o-transition:  opacity <?php echo $styledata[57]; ?>s linear;
                    transition:  opacity <?php echo $styledata[57]; ?>s linear;
                    -webkit-animation-duration: <?php echo $styledata[57]; ?>s;
                    -moz-animation-duration: <?php echo $styledata[57]; ?>s;
                    -ms-animation-duration: <?php echo $styledata[57]; ?>s;
                    -o-animation-duration: <?php echo $styledata[57]; ?>s;
                    animation-duration: <?php echo $styledata[57]; ?>s;
                }
                .oxilab-flip-box-body-<?php echo $styleid; ?>{
                    max-width: <?php echo $styledata[45]; ?>px;
                    width: 100%;
                    margin: 0 auto;
                    position: relative;   
                }
                .oxilab-flip-box-body-<?php echo $styleid; ?>:after {
                    padding-bottom: <?php echo $styledata[47] / $styledata[45] * 100; ?>%;
                    content: "";
                    display: block;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>{
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: block;
                    background-color: <?php echo $styledata[5]; ?>;
                    -webkit-border-radius: <?php echo $styledata[69]; ?>px;
                    -moz-border-radius: <?php echo $styledata[69]; ?>px;
                    -ms-border-radius: <?php echo $styledata[69]; ?>px;
                    -o-border-radius: <?php echo $styledata[69]; ?>px;
                    border-radius: <?php echo $styledata[69]; ?>px;
                    overflow: hidden;
                    -webkit-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -moz-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -ms-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -o-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data{           
                    position: absolute;
                    left: 0%;
                    top: 50%;            
                    padding: <?php echo $styledata[71]; ?>px <?php echo $styledata[73]; ?>px;
                    -webkit-transform: translateY(-50%);
                    -ms-transform: translateY(-50%);
                    -moz-transform: translateY(-50%);
                    -o-transform: translateY(-50%);
                    transform: translateY(-50%);
                    right: 0;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data .oxilab-icon{
                    display: block;
                    text-align: center; 
                    padding:<?php echo $styledata[133]; ?>px <?php echo $styledata[135]; ?>px;  
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data .oxilab-icon-data{
                    display: inline-block;  
                    background-color: <?php echo $styledata[9]; ?>;
                    width:<?php echo $styledata[79]; ?>px;
                    height:<?php echo $styledata[79]; ?>px;
                    -webkit-border-radius: <?php echo $styledata[81]; ?>px;
                    -moz-border-radius: <?php echo $styledata[81]; ?>px;
                    -ms-border-radius: <?php echo $styledata[81]; ?>px;
                    -o-border-radius: <?php echo $styledata[81]; ?>px;
                    border-radius: <?php echo $styledata[81]; ?>px;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data .oxilab-icon-data .oxi-icons{            
                    line-height: <?php echo $styledata[79]; ?>px;
                    font-size: <?php echo $styledata[77]; ?>px;       
                    color:<?php echo $styledata[7]; ?>;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data .oxilab-heading{
                    display: block;
                    color:<?php echo $styledata[11]; ?>;
                    text-align: <?php echo $styledata[91]; ?>;            
                    font-size: <?php echo $styledata[83]; ?>px;
                    font-family: <?php echo $this->font_familly($styledata[85]); ?>;
                    font-weight: <?php echo $styledata[89]; ?>;
                    font-style:<?php echo $styledata[87]; ?>;
                    padding: <?php echo $styledata[93]; ?>px <?php echo $styledata[99]; ?>px <?php echo $styledata[95]; ?>px <?php echo $styledata[97]; ?>px;  
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>{
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: block;
                    background-color: <?php echo $styledata[13]; ?>;
                    -webkit-border-radius: <?php echo $styledata[69]; ?>px;
                    -moz-border-radius: <?php echo $styledata[69]; ?>px;
                    -ms-border-radius: <?php echo $styledata[69]; ?>px;
                    -o-border-radius: <?php echo $styledata[69]; ?>px;
                    border-radius: <?php echo $styledata[69]; ?>px;
                    overflow: hidden;
                    -webkit-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -moz-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -ms-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -o-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data{           
                    position: absolute;
                    left: 0%;
                    right: 0;
                    top: 50%;            
                    padding: <?php echo $styledata[101]; ?>px <?php echo $styledata[103]; ?>px;
                    -webkit-transform: translateY(-50%);
                    -ms-transform: translateY(-50%);
                    -moz-transform: translateY(-50%);
                    -o-transform: translateY(-50%);
                    transform: translateY(-50%);           
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-heading{
                    display: block;
                    color:<?php echo $styledata[15]; ?>;  
                    text-align: <?php echo $styledata[145]; ?>;            
                    font-size: <?php echo $styledata[137]; ?>px;
                    font-family: <?php echo $this->font_familly($styledata[139]); ?>;
                    font-weight: <?php echo $styledata[143]; ?>;
                    font-style:<?php echo $styledata[141]; ?>;
                    padding: <?php echo $styledata[147]; ?>px <?php echo $styledata[153]; ?>px <?php echo $styledata[149]; ?>px <?php echo $styledata[151]; ?>px;  

                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-icon{
                    display: block;
                    text-align: center; 
                    padding: <?php echo $styledata[129]; ?>px <?php echo $styledata[131]; ?>px; 
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-icon-data{
                    display: inline-block;  
                    Border-bottom-color: <?php echo $styledata[17]; ?>;
                    width: <?php echo $styledata[127]; ?>px;
                    height: <?php echo $styledata[127]; ?>px;
                    border-bottom: <?php echo $styledata[105]; ?>px solid;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-icon-data .oxi-icons{            
                    line-height:<?php echo $styledata[127]; ?>px;
                    font-size: <?php echo $styledata[125]; ?>px; 
                    color:<?php echo $styledata[17]; ?>;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-info{
                    display: block;
                    color:<?php echo $styledata[19]; ?>; 
                    text-align: <?php echo $styledata[115]; ?>;            
                    font-size: <?php echo $styledata[107]; ?>px;          
                    font-family: <?php echo $this->font_familly($styledata[109]); ?>;
                    font-weight: <?php echo $styledata[113]; ?>;
                    font-style:<?php echo $styledata[111]; ?>;
                    padding:<?php echo $styledata[117]; ?>px <?php echo $styledata[123]; ?>px <?php echo $styledata[119]; ?>px <?php echo $styledata[121]; ?>px;                   
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-button{
                    display: block;
                    text-align: <?php echo $styledata[169]; ?>;
                    padding: <?php echo $styledata[171]; ?>px <?php echo $styledata[177]; ?>px <?php echo $styledata[173]; ?>px <?php echo $styledata[175]; ?>px;  
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-button-data{
                    display: inline-block;  
                    color: <?php echo $styledata[21]; ?>;
                    background-color:  <?php echo $styledata[23]; ?>;  
                    font-size: <?php echo $styledata[155]; ?>px;            
                    font-family: <?php echo $this->font_familly($styledata[157]); ?>;
                    font-weight: <?php echo $styledata[161]; ?>;
                    font-style:<?php echo $styledata[159]; ?>;
                    padding: <?php echo $styledata[163]; ?>px <?php echo $styledata[165]; ?>px;  
                    -webkit-border-radius: <?php echo $styledata[167]; ?>px;
                    -moz-border-radius: <?php echo $styledata[167]; ?>px;
                    -ms-border-radius: <?php echo $styledata[167]; ?>px;
                    -o-border-radius: <?php echo $styledata[167]; ?>px;
                    border-radius: <?php echo $styledata[167]; ?>px;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-button-data:hover{
                    background-color: <?php echo $styledata[27]; ?>;
                    color:  <?php echo $styledata[25]; ?>;
                }
        <?php echo $styledata[179]; ?>;
            </style>
        </div>
        <?php
    }

}
