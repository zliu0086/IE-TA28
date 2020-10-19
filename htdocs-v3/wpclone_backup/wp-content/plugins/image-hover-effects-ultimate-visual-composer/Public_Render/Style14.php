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

class Style14 extends Public_Render {

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
                            if ($filesdata[9] == '' && $filesdata[11] != '') {
                                echo '<a href="' . $filesdata[11] . '" target="' . $styledata[53] . '">';
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
                                                    <div class="oxilab-flip-box-<?php echo $styleid; ?>-data2">
                                                        <div class="oxilab-icon">
                                                            <div class="oxilab-icon-data">
                                                                <div class="oxilab-span">
                                                                    <?php echo $this->text_render($filesdata[3]); ?>
                                                                </div>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="oxilab-flip-box-<?php echo $styleid; ?>-data"> 
                                                        <div class="oxilab-heading">
                                                            <?php echo $this->text_render($filesdata[1]); ?>
                                                        </div>
                                                        <div class="oxilab-info">
                                                            <?php echo $this->text_render($filesdata[15]); ?>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="oxilab-flip-box-back">
                                                <div class="oxilab-flip-box-back-<?php echo $styleid; ?>">
                                                    <div class="oxilab-flip-box-back-<?php echo $styleid; ?>-data2">
                                                        <div class="oxilab-icon">
                                                            <div class="oxilab-icon-data">
                                                                <?php echo $this->font_awesome_render($filesdata[19]) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="oxilab-flip-box-back-<?php echo $styleid; ?>-data">
                                                        <div class="oxilab-heading">
                                                            <?php echo $this->text_render($filesdata[17]); ?>
                                                        </div>
                                                        <div class="oxilab-info">
                                                            <?php echo $this->text_render($filesdata[7]); ?>
                                                        </div>
                                                        <?php
                                                        if ($filesdata[9] != '') {
                                                            echo '<a href="' . $filesdata[11] . '" target="' . $styledata[53] . '">';
                                                            echo '<div class="oxilab-button">
                                                                    <div class="oxilab-button-data">
                                                                    ' . $this->text_render($filesdata[9]) . '
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
                            <?php echo $fileslinkend;
                             echo $this->admin_edit_panel($value['id']);
                            ?>
                        </div>
                        <style>
                <?php
                if ($filesdata[5] != '') {
                    echo '.oxilab-flip-box-body-' . $styleid . '-' . $value['id'] . ' .oxilab-flip-box-front{
background: linear-gradient(' . $styledata[5] . ', ' . $styledata[5] . '), url("' . $filesdata[5] . '");
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;
}';
                }
                if ($filesdata[13] != '') {
                    echo '.oxilab-flip-box-body-' . $styleid . '-' . $value['id'] . ' .oxilab-flip-box-back{
background: linear-gradient(' . $styledata[17] . ', ' . $styledata[17] . '), url("' . $filesdata[13] . '");
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
                .oxilab-flip-box-body-<?php echo $styleid; ?> .oxilab-flip-box-front{
                    border-width: <?php echo $styledata[125]; ?>px;
                    border-style:<?php echo $styledata[127]; ?>; 
                    -webkit-border-radius: <?php echo $styledata[129]; ?>px;
                    -moz-border-radius: <?php echo $styledata[129]; ?>px;
                    -ms-border-radius: <?php echo $styledata[129]; ?>px;
                    -o-border-radius: <?php echo $styledata[129]; ?>px;
                    border-radius: <?php echo $styledata[129]; ?>px;
                    border-color: <?php echo $styledata[7]; ?>;
                    background-color: <?php echo $styledata[5]; ?>;
                    overflow: hidden;
                    -webkit-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -moz-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -ms-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -o-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>{
                    position: absolute;
                    top: <?php echo $styledata[71]; ?>px;
                    left: <?php echo $styledata[73]; ?>px;
                    right: <?php echo $styledata[73]; ?>px;
                    bottom: <?php echo $styledata[71]; ?>px;
                    border-width: <?php echo $styledata[179]; ?>px;
                    border-style:<?php echo $styledata[181]; ?>; 
                    border-color: <?php echo $styledata[27]; ?>;
                    display: block;
                    -webkit-border-radius: <?php echo $styledata[183]; ?>px;
                    -moz-border-radius: <?php echo $styledata[183]; ?>px;
                    -ms-border-radius: <?php echo $styledata[183]; ?>px;
                    -o-border-radius: <?php echo $styledata[183]; ?>px;
                    border-radius: <?php echo $styledata[183]; ?>px;
                    overflow: hidden;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data2{
                    position: absolute;
                    top: 0%;   
                    left: 50%;   
                    -webkit-transform: translateX(-50%);
                    -ms-transform: translateX(-50%);
                    -moz-transform: translateX(-50%);
                    -o-transform: translateX(-50%);
                    transform: translateX(-50%);
                    background-color: <?php echo $styledata[11]; ?>;
                    height: <?php echo $styledata[75]; ?>px;
                    width: <?php echo $styledata[79]; ?>px; 
                    -webkit-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -moz-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -ms-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -o-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -webkit-box-shadow: 0 0 3px 0 #666666;
                    -moz-box-shadow: 0 0 3px 0 #666666;
                    -ms-box-shadow: 0 0 3px 0 #666666;
                    -o-box-shadow: 0 0 3px 0 #666666;
                    box-shadow: 0 0 3px 0 #666666;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data2 .oxilab-icon{
                    position: absolute;
                    bottom:  0; 
                    width: 100%;
                    display: block;
                    text-align: center;  
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data2 .oxilab-icon-data{
                    display: inline-block;  
                    width: <?php echo $styledata[79]; ?>px;
                    height: <?php echo $styledata[79]; ?>px;
                    -webkit-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -moz-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -ms-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    -o-border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                    border-radius: 0 0 <?php echo $styledata[81]; ?>px <?php echo $styledata[81]; ?>px;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data2 .oxilab-icon-data .oxilab-span{            
                    line-height:<?php echo $styledata[79]; ?>px;
                    font-size: <?php echo $styledata[77]; ?>px;
                    color: <?php echo $styledata[9]; ?>;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data{           
                    position: absolute;
                    left: 0%;
                    top: <?php echo $styledata[75]; ?>px;  
                    padding: <?php echo $styledata[185]; ?>px <?php echo $styledata[187]; ?>px;
                    right: 0;
                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data .oxilab-heading{
                    display: block;
                    color: <?php echo $styledata[13]; ?>;  
                    text-align: <?php echo $styledata[91]; ?>;            
                    font-size: <?php echo $styledata[83]; ?>px;
                    font-family: <?php echo $this->font_familly($styledata[85]); ?>;
                    font-weight: <?php echo $styledata[89]; ?>;
                    font-style:<?php echo $styledata[87]; ?>;
                    padding: <?php echo $styledata[93]; ?>px <?php echo $styledata[99]; ?>px <?php echo $styledata[95]; ?>px <?php echo $styledata[97]; ?>px;  

                }
                .oxilab-flip-box-<?php echo $styleid; ?>-data .oxilab-info{
                    display: block;
                    color: <?php echo $styledata[15]; ?>; 
                    text-align: <?php echo $styledata[147]; ?>;            
                    font-size: <?php echo $styledata[139]; ?>px;
                    font-family: <?php echo $this->font_familly($styledata[141]); ?>;
                    font-weight: <?php echo $styledata[145]; ?>;
                    font-style:<?php echo $styledata[143]; ?>;
                    padding: <?php echo $styledata[149]; ?>px <?php echo $styledata[155]; ?>px <?php echo $styledata[151]; ?>px <?php echo $styledata[153]; ?>px; 

                }
                .oxilab-flip-box-body-<?php echo $styleid; ?> .oxilab-flip-box-back{
                    border-width: <?php echo $styledata[131]; ?>px;
                    border-style:<?php echo $styledata[133]; ?>; 
                    border-color: <?php echo $styledata[19]; ?>;   
                    background-color: <?php echo $styledata[17]; ?>;  
                    -webkit-border-radius: <?php echo $styledata[129]; ?>px;
                    -moz-border-radius: <?php echo $styledata[129]; ?>px;
                    -ms-border-radius: <?php echo $styledata[129]; ?>px;
                    -o-border-radius: <?php echo $styledata[129]; ?>px;
                    border-radius: <?php echo $styledata[129]; ?>px;
                    overflow: hidden;
                    -webkit-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -moz-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -ms-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    -o-box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                    box-shadow: <?php echo $styledata[61]; ?>px <?php echo $styledata[63]; ?>px <?php echo $styledata[65]; ?>px <?php echo $styledata[67]; ?>px <?php echo $styledata[59]; ?>;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>{
                    position: absolute;
                    top: <?php echo $styledata[101]; ?>px;
                    left: <?php echo $styledata[103]; ?>px;
                    right: <?php echo $styledata[103]; ?>px;
                    bottom: <?php echo $styledata[101]; ?>px;
                    border-width: <?php echo $styledata[189]; ?>px;
                    border-style:<?php echo $styledata[191]; ?>; 
                    border-color: <?php echo $styledata[29]; ?>;
                    display: block;
                    -webkit-border-radius: <?php echo $styledata[193]; ?>px;
                    -moz-border-radius: <?php echo $styledata[193]; ?>px;
                    -ms-border-radius: <?php echo $styledata[193]; ?>px;
                    -o-border-radius: <?php echo $styledata[193]; ?>px;
                    border-radius: <?php echo $styledata[193]; ?>px;
                    overflow: hidden;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data2{
                    position: absolute;
                    top: 0%;   
                    left: 50%;   
                    -webkit-transform: translateX(-50%);
                    -ms-transform: translateX(-50%);
                    -moz-transform: translateX(-50%);
                    -o-transform: translateX(-50%);
                    transform: translateX(-50%);
                    height: <?php echo $styledata[203]; ?>px; 
                    width: <?php echo $styledata[201]; ?>px; 
                    background-color: <?php echo $styledata[41]; ?>; 
                    -webkit-border-radius: 0 0 <?php echo $styledata[209]; ?>px <?php echo $styledata[209]; ?>px;
                    -moz-border-radius: 0 0 <?php echo $styledata[209]; ?>px <?php echo $styledata[209]; ?>px;
                    -ms-border-radius: 0 0 <?php echo $styledata[209]; ?>px <?php echo $styledata[209]; ?>px;
                    -o-border-radius: 0 0 <?php echo $styledata[209]; ?>px <?php echo $styledata[209]; ?>px;
                    border-radius: 0 0 <?php echo $styledata[209]; ?>px <?php echo $styledata[209]; ?>px;
                    -webkit-box-shadow: 0 0 3px 0 #666666;
                    -moz-box-shadow: 0 0 3px 0 #666666;
                    -ms-box-shadow: 0 0 3px 0 #666666;
                    -o-box-shadow: 0 0 3px 0 #666666;
                    box-shadow: 0 0 3px 0 #666666;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data2 .oxilab-icon{
                    position: absolute;
                    bottom:  0; 
                    width: 100%;
                    display: block;
                    text-align: center;  
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data2 .oxilab-icon-data{
                    display: inline-block;  
                    width: <?php echo $styledata[201]; ?>px; 
                    height: <?php echo $styledata[201]; ?>px; 
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data2 .oxilab-icon-data .oxi-icons{            
                    line-height: <?php echo $styledata[201]; ?>px; 
                    font-size: <?php echo $styledata[199]; ?>px; 
                    color: <?php echo $styledata[39]; ?>; 
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data{           
                    position: absolute;
                    left: 0%;
                    right: 0;
                    top: <?php echo $styledata[203]; ?>px;          
                    padding: <?php echo $styledata[195]; ?>px <?php echo $styledata[197]; ?>px;     
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-heading{
                    display: block;
                    color: <?php echo $styledata[21]; ?>;
                    text-align: <?php echo $styledata[165]; ?>;            
                    font-size: <?php echo $styledata[157]; ?>px;          
                    font-family: <?php echo $this->font_familly($styledata[159]); ?>;
                    font-weight: <?php echo $styledata[163]; ?>;
                    font-style:<?php echo $styledata[161]; ?>;
                    padding:<?php echo $styledata[167]; ?>px <?php echo $styledata[173]; ?>px <?php echo $styledata[169]; ?>px <?php echo $styledata[171]; ?>px;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-info{
                    display: block;
                    color: <?php echo $styledata[25]; ?>;
                    text-align: <?php echo $styledata[115]; ?>;            
                    font-size: <?php echo $styledata[107]; ?>px;          
                    font-family: <?php echo $this->font_familly($styledata[109]); ?>;
                    font-weight: <?php echo $styledata[113]; ?>;
                    font-style:<?php echo $styledata[111]; ?>;
                    padding:<?php echo $styledata[117]; ?>px <?php echo $styledata[123]; ?>px <?php echo $styledata[119]; ?>px <?php echo $styledata[121]; ?>px;                 

                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-button{
                    display: block;
                    text-align: <?php echo $styledata[225]; ?>;
                    padding: <?php echo $styledata[227]; ?>px <?php echo $styledata[233]; ?>px <?php echo $styledata[229]; ?>px <?php echo $styledata[231]; ?>px;  

                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-button-data{
                    display: inline-block;     
                    color: <?php echo $styledata[31]; ?>;
                    background-color:  <?php echo $styledata[33]; ?>;
                    font-size: <?php echo $styledata[211]; ?>px;            
                    font-family: <?php echo $this->font_familly($styledata[213]); ?>;
                    font-weight: <?php echo $styledata[217]; ?>;
                    font-style:<?php echo $styledata[215]; ?>;
                    padding: <?php echo $styledata[219]; ?>px <?php echo $styledata[221]; ?>px; 
                    -webkit-border-radius: <?php echo $styledata[223]; ?>px;
                    -moz-border-radius: <?php echo $styledata[223]; ?>px;
                    -ms-border-radius: <?php echo $styledata[223]; ?>px;
                    -o-border-radius: <?php echo $styledata[223]; ?>px;
                    border-radius: <?php echo $styledata[223]; ?>px;
                }
                .oxilab-flip-box-back-<?php echo $styleid; ?>-data .oxilab-button-data:hover{
                    background-color: <?php echo $styledata[37]; ?>;
                    color:  <?php echo $styledata[35]; ?>;
                }
                <?php echo $styledata[235]; ?>;
            </style>
        </div>
        <?php
    }

}
