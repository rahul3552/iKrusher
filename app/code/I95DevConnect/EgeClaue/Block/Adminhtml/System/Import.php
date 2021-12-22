<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace I95DevConnect\EgeClaue\Block\Adminhtml\System;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Design\ThemeInterface;

class Import extends \MGS\Mpanel\Block\Adminhtml\System\Import
{
    public function isLocalhost()
    {
        $whitelist = array(
            '127.0.0.1',
            'localhost',
            '::1'
        );
        return true;
    }


    public function getTheme()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $activeKey = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('active_theme/activate/claue');
        $themeId = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()
        );

    /** @var $theme ThemeInterface */
        $theme = $objectManager->get('Magento\Framework\View\Design\Theme\ThemeProviderInterface')->getThemeById($themeId);

        return $theme->getData();
    }

    public function _getHeaderCommentHtml($element)
    {
        $html = '';
        if (is_dir($this->_dir)) {
            if ($dh = opendir($this->_dir)) {
                $dirs = scandir($this->_dir);
                foreach ($dirs as $theme) {
                    if (($theme !='') && ($theme!='.') && ($theme!='..')) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $activeKey = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('active_theme/activate/claue');
                        $themeName = $this->convertString($theme);
                        $html .= '<div>';
                        $html .= '<div class="section-config"><div class="entry-edit-head admin__collapsible-block"><span class="entry-edit-head-link" id="mgstheme_import_'.$theme.'-link"></span><a onclick="Fieldset.toggleCollapse(\'mgstheme_import_'.$theme.'\', \''.$this->getUrl('adminhtml/system_config/state').'\'); return false;" href="#mgstheme_import_'.$theme.'-link" id="mgstheme_import_'.$theme.'-head">'.$themeName.'</a></div><input type="hidden" value="0" name="config_state[mgstheme_import_'.$theme.']" id="mgstheme_import_'.$theme.'-state"><fieldset id="mgstheme_import_'.$theme.'" class="config admin__collapsible-block" style="display:none"><legend>'.$themeName.'</legend>';
                        $themeDir = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/MGS/Mpanel/data/themes/'.$theme.'/homes');
                        $fileHomes = array();
                        if (is_dir($themeDir)) {
                            if ($dhHome = opendir($themeDir)) {
                                while ($fileHomes[] = readdir($dhHome)) {
                                }
                                sort($fileHomes);
                                if (count($fileHomes)>0) {
                                    $html .= '
									<style type="text/css">
									#mainContainer, #footerArea {
										clear: both;
										width: 100%;
										margin: 10px;
										float: left;
									}
									hr.brclr {
										border-bottom: 1px solid #e3e3e3;
										margin: 10px 0;
										margin-top: 0px;
										background-color: #e3e3e3;
										color: #e3e3e3;
										height: 2px;
										margin: 10px 0;
										border: none;
									}
									.imagecontainernewmn {
										height: 219px;
										overflow: hidden;
										position: relative;
									}
									.imagecontainernewmn:hover{
										cursor:pointer;
									}
									.imagecontainernewmn:hover > .imghover {
										cursor: pointer;
										display: block;
										background-color: #00000073;
										width: 100%;
										position: absolute;
										height: 100%;
									}
									#leftNav {
										float: left;
										width: 20%;
										margin-right: 15px;
										border-right: 2px solid #e3e3e3;
										text-align: center;
										padding-right: 20px;
										height: 1470px;
										overflow-y: scroll;
									}
									#leftNav::-webkit-scrollbar {
										width: 10px;
									}
                                    #leftNav::-webkit-scrollbar-track {
                                      background: #f8f8f8;
                                    }
                                    #leftNav::-webkit-scrollbar-thumb {
                                      background: #eb5202;
                                    }
									#leftNav img:before{
										content:"Select";
										display:none;
									}
									#leftNav img:hover > :before {
										display:block;
									}
									#rightDisplay {
										float: right;
										width: 72%;
										margin: 0;
										text-align: center;
									}
									#rightDisplay img{
										width: 100%;
									}
									.button_container{
										display:none;
									}
									.imghover{
										display:none;
									}
									.previewbtn {
										background-color: #ffffff;
										width: 130px;
										margin: 0 auto;
										height: 20px;
										padding: 10px 20px;
										border-radius: 20px;
										color: #1a78eb;
									}
									h3.newthemename{
										color: #ffffff;
										margin: 10px 0;
										margin-top: 70px;
									}
									.prvlft {
										float: left;
									}
									.btnright {
										float: right;
									}
									.btnright button{
										margin-right: 10px;
										background-color: transparent;
										border: none;
										color: #0041fa;
									}
									.lft-right-container {
										width: 100%;
										float: left;
										margin-bottom: 10px;
									}
                                .newCcontainer {
                                  display: block;
                                    position: relative;
                                    padding-left: 0px;
                                    margin-bottom: 12px;
                                    cursor: pointer;
                                    font-size: 22px;
                                    -webkit-user-select: none;
                                    -moz-user-select: none;
                                    -ms-user-select: none;
                                    user-select: none;
                                    position: absolute;
                                    height: 25px;
                                    background-color: #eb5202;
                                    width: 25px;
                                }

                                .newCcontainer input {
                                  position: absolute;
                                    opacity: 0;
                                    cursor: pointer;
                                    height: 0;
                                    width: 0;
                                }
                                .newCcontainer input:checked ~ .checkmark {
                                    background-color: #2196F3;
                                }
                                .checkmark {
                                  position: absolute;
                                  left: 9px;
                                    top: 5px;
                                    width: 5px;
                                    height: 10px;
                                    border: solid white;
                                    border-width: 0 3px 3px 0;
                                    -webkit-transform: rotate(45deg);
                                    -ms-transform: rotate(45deg);
                                    transform: rotate(45deg);}

                                .checkmark:after {
                                  content: "";
                                  position: absolute;
                                  display: none;
                                }

                                .newCcontainer input:checked ~ .checkmark:after {
                                  display: block;
                                }

                                .newCcontainer .checkmark:after {
                                  left: 9px;
                                  top: 5px;
                                  width: 5px;
                                  height: 10px;
                                  border: solid white;
                                  border-width: 0 3px 3px 0;
                                  -webkit-transform: rotate(45deg);
                                  -ms-transform: rotate(45deg);
                                  transform: rotate(45deg);
                                }
								</style>
									<div id="mainContainer"> <div id="leftNav">';
                                    foreach ($fileHomes as $fileHome) {
                                        $file_parts_home = pathinfo($themeDir.'/'.$fileHome);
                                        if (isset($file_parts_home['extension']) && $file_parts_home['extension']=='xml') {
                                            $homeName = $home = str_replace('.xml', '', $fileHome);
                                            $homeName = $this->convertString($homeName);
                                            $html .= '
											<div class="imagecontainernewmn">';

                                            $activeClaueLayout = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('mgs/mpanel/home_layout');
                                            $activatedTheme = $this->getTheme();
                                            if ($home == $activeClaueLayout && $activatedTheme['theme_path'] == "Mgs/" . $theme) {
                                                $html .= '
                                                    <label class="newCcontainer">
                                                      <input id="themeapplied" name="themeapplied type="radio" checked="checked">
                                                      <span class="checkmark"></span>
                                                    </label>
                                                ';
                                            }
                                            
                                            $html .= '
											<div class="imghover">
												<h3 class="newthemename">'.$homeName.'</h3>
												<div type="button" id="'.$home.'" class="previewbtn" title="'.$home.'" onclick="showImage(`'.$home.'.png`, `' . $theme . '`);"> Preview Theme </div>
                                            </div>
                                        
											<img src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/claue/homes/'.$home.'.png" width="250" alt="'.$home.'1.png" title="'.$home.'.png" onclick="showImage(`'.$home.'1.png`, `' . $theme . '`);" id="'.$home.'1"   />';
                                            //<img src="images/bridget_moynahan_00_tn.jpg" alt="bridget_moynahan_00.jpg" title="bridget_moynahan_00.jpg" onclick="showImage(`bridget_moynahan_00.jpg`);" />
                                            $html.='<div class="button_container '.$home.'1">';
                                            if ($storeId = $this->getRequest()->getParam('store')) {
                                                $url = $this->getUrl('adminhtml/mpanel/import', ['store'=>$storeId, 'theme'=>$theme, 'home'=>$home]);
                                            } elseif ($websiteId = $this->getRequest()->getParam('website')) {
                                                $url = $this->getUrl('adminhtml/mpanel/import', ['website'=>$websiteId, 'theme'=>$theme, 'home'=>$home]);
                                            } else {
                                                $url = $this->getUrl('adminhtml/mpanel/import', ['theme'=>$theme, 'home'=>$home]);
                                            }
                                            if (($theme=='claue' || $theme=='claue_rtl') && !$this->isLocalhost()) {
                                                if ($activeKey =='') {
                                                    $html .= '<button data-ui-id="widget-button-0" onclick="setLocation(\''.$url.'\')" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'"><span>'.__('Import %1', $homeName).'</span></button>';
                                                } else {
                                                    $html .= '<button data-ui-id="widget-button-0" onclick="return false;" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'" style="margin-right:10px" disabled="disabled"><span>'.__('Import %1', $homeName).'</span></button><a href="'.$this->getUrl('adminhtml/system_config/edit/section/active_theme').'" style="text-decoration:none"><span style="color:#ff0000">'.__('Activation is required.').'</span></a>';
                                                }
                                            } else {
                                                $html .= '<button data-ui-id="widget-button-0" onclick="setLocation(\''.$url.'\')" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'"><span>'.__('Import %1', $homeName).'</span></button>';
                                            }
                                            $html.='</div></div><hr class="brclr" />';
                                        }
                                    }
                                    //changed
                                    // foreach ($fileHomes as $fileHome){
                                    //     $file_parts_home = pathinfo($themeDir.'/'.$fileHome);
                                    //     if(isset($file_parts_home['extension']) && $file_parts_home['extension']=='xml') {
                                    //         $homeName = $home = str_replace('.xml', '', $fileHome);
                                    //         $homeName = $this->convertString($homeName);
                                    //         if($storeId = $this->getRequest()->getParam('store')){
                                    //             $url = $this->getUrl('adminhtml/mpanel/import', ['store'=>$storeId, 'theme'=>$theme, 'home'=>$home]);
                                    //         }
                                    //         elseif($websiteId = $this->getRequest()->getParam('website')){
                                    //             $url = $this->getUrl('adminhtml/mpanel/import', ['website'=>$websiteId, 'theme'=>$theme, 'home'=>$home]);
                                    //         }else{
                                    //             $url = $this->getUrl('adminhtml/mpanel/import', ['theme'=>$theme, 'home'=>$home]);
                                    //         }
                                    //         if(($theme=='claue' || $theme=='claue_rtl') && !$this->isLocalhost()){

                                    //             if($activeKey ==''){
                                    //                 $html .= '<button data-ui-id="widget-button-0" onclick="setLocation(\''.$url.'\')" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'"><span>'.__('Import %1', $homeName).'</span></button>';
                                    //             }else{
                                    //                 $html .= '<button data-ui-id="widget-button-0" onclick="return false;" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'" style="margin-right:10px" disabled="disabled"><span>'.__('Import %1', $homeName).'</span></button><a href="'.$this->getUrl('adminhtml/system_config/edit/section/active_theme').'" style="text-decoration:none"><span style="color:#ff0000">'.__('Activation is required.').'</span></a>';
                                    //             }
                                    //         }else{
                                    //             $html .= '<button data-ui-id="widget-button-0" onclick="setLocation(\''.$url.'\')" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'"><span>'.__('Import %1', $homeName).'</span></button>';
                                    //         }
                                    //     }}
                                    //changed end
                                    $html .= '</div>
										<div id="rightDisplay">';
                                    $html .= '
									<div class="lft-right-container">
									<div class="prvlft"> Preview </div>
									<div class="btnright">
                                    ';
                                    if(empty($activeClaueLayout)){
                                        if($theme == 'claue') {
                                            $activeClaueLayout = "home_banner";
                                        } else {
                                            $activeClaueLayout = "home_boxed";
                                        }
                                    }
                                    if ($storeId = $this->getRequest()->getParam('store')) {
                                        $homeLayoutUrl = $this->getUrl('adminhtml/mpanel/import', ['store'=>$storeId, 'theme'=>$theme, 'home'=>$activeClaueLayout]);
                                    } elseif ($websiteId = $this->getRequest()->getParam('website')) {
                                        $homeLayoutUrl = $this->getUrl('adminhtml/mpanel/import', ['website'=>$websiteId, 'theme'=>$theme, 'home'=>$activeClaueLayout]);
                                    } else {
                                        $homeLayoutUrl = $this->getUrl('adminhtml/mpanel/import', ['theme'=>$theme, 'home'=>$activeClaueLayout]);
                                    }
                                    $html .= '
									<button id="curImagebtn_' . $theme . '" data-ui-id="widget-button-0" onclick="setLocation(\''.$homeLayoutUrl.'\')" class="action-default scalable" type="button" title="home_banner"><span>Apply Theme</span></button>
									</div>
									</div>
                                    ';

                                    $html .= '<img id="currentImg_' . $theme . '" src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/claue/homes/'. $activeClaueLayout .'.png" width="250" id="small_image_new"  />
									</div>
									</div>

									<table style="display:none"><tbody>';
                                    foreach ($fileHomes as $fileHome) {
                                        $file_parts_home = pathinfo($themeDir.'/'.$fileHome);
                                        if (isset($file_parts_home['extension']) && $file_parts_home['extension']=='xml') {
                                            $html .= '<tr>';
                                            $html .= '<td style="padding: 0 0px 20px 0;border-right: 1px solid #cccccc;width: 270px;">';
                                            $homeName = $home = str_replace('.xml', '', $fileHome);
                                            $homeName = $this->convertString($homeName);
                                            $html .= '<img src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/claue/homes/'.$home.'.png" width="250" id="small_image_new"  />';
                                            $html .= '</td>

                                            <td style="padding-left: 20px;">
                                                <div class="image_preview_container">
                                                    <div class="image_preview"> </div>
                                                </div>


                                            ';

                                            if ($storeId = $this->getRequest()->getParam('store')) {
                                                $url = $this->getUrl('adminhtml/mpanel/import', ['store'=>$storeId, 'theme'=>$theme, 'home'=>$home]);
                                            } elseif ($websiteId = $this->getRequest()->getParam('website')) {
                                                $url = $this->getUrl('adminhtml/mpanel/import', ['website'=>$websiteId, 'theme'=>$theme, 'home'=>$home]);
                                            } else {
                                                $url = $this->getUrl('adminhtml/mpanel/import', ['theme'=>$theme, 'home'=>$home]);
                                            }
                                            if (($theme=='claue' || $theme=='claue_rtl') && !$this->isLocalhost()) {
                                                if ($activeKey!='') {
                                                    $html .= '<button data-ui-id="widget-button-0" onclick="setLocation(\''.$url.'\')" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'"><span>'.__('Import %1', $homeName).'</span></button>';
                                                } else {
                                                    $html .= '<button data-ui-id="widget-button-0" onclick="return false;" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'" style="margin-right:10px" disabled="disabled"><span>'.__('Import %1', $homeName).'</span></button><a href="'.$this->getUrl('adminhtml/system_config/edit/section/active_theme').'" style="text-decoration:none"><span style="color:#ff0000">'.__('Activation is required.').'</span></a>';
                                                }
                                            } else {
                                                $html .= '<button data-ui-id="widget-button-0" onclick="setLocation(\''.$url.'\')" class="action-default scalable" type="button" title="'.__('Import %1', $homeName).'"><span>'.__('Import %1', $homeName).'</span></button>';
                                            }

                                            $html .= '</td></tr>';
                                        }
                                    }
                                    $html .= '</tbody></table>';
                                }
                            }
                        }

                        $html .= '</fieldset>
						<script type="text/javascript">
						//<![CDATA[require([\'prototype\'],function(){Fieldset.applyCollapse(\'mgstheme_import_'.$theme.'\');});//]]>
						$(".")
						</script>

</div>';

                        $html .= '</div>
		<script type="text/javascript">
			function showImage(imgName, theme) {
                var importPath = "' . $this->getUrl('adminhtml/mpanel/import/') . 'theme/"
                importPath = importPath + theme;
				document.getElementById(`curImagebtn_` + theme).setAttribute(
                        "onclick",
                        "setLocation(\'" + importPath + "/home/" + event.target.id + "\')"
                    )

				document.getElementById(`curImagebtn_` + theme).setAttribute("title", event.target.id)
				var curImage = document.getElementById(`currentImg_` + theme);
				var thePath = "' . $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'mgs/claue/homes/"
				var theSource = thePath + imgName;
				curImage.src = theSource;
				curImage.alt = imgName;
				curImage.title = imgName;
			}
		</script>

';
                    }
                }

                closedir($dh);
            }
        }

        return $html;
    }
}
