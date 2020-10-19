<template>
    <div class="sui-box" id="configs">
        <div class="sui-box-header">
            <h3 class="sui-box-title">
                {{__("Preset Configs")}}
            </h3>
            <div class="sui-actions-right">
                <input type="file" ref="file" @change="file_changed" class="invisible" id="json_file">
                <button type="button" class="sui-button sui-button-ghost"
                        :class="{'sui-button-onload-text':uploading}"
                        id="open-uploader">
                    <span class="sui-button-text-default">
                        <i class="sui-icon-upload-cloud" aria-hidden="true"></i> {{__('Upload')}}
                    </span>
                    <span class="sui-button-text-onload">
		                <i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
		                {{__('Importing...')}}
	                </span>
                </button>
                <button
                        class="sui-button sui-button-blue"
                        data-modal-open="new-config"
                        data-modal-open-focus="configs"
                        data-modal-close-focus="configs"
                        data-modal-mask="false"
                        data-esc-close="true"
                >
                    <i class="sui-icon-save" aria-hidden="true"></i> {{__('Save new')}}
                </button>
            </div>
        </div>
        <div class="sui-box-body" :class="{'no-padding-bottom':is_shown==='no'}">
            <p>
                {{__('Configs bundle your Defender settings and make them available to download and apply on your other sites.')}}
            </p>
            <div class="grid grid-cols-12" style="min-height: 130px" v-if="is_shown === 'no'">
                <div class="col-span-2 relative" v-if="!isWhitelabelEnabled()">
                    <img class="object-contain h-full w-auto absolute bottom-0"
                         :src="assetUrl('assets/img/auditting-man.svg')">
                </div>
                <div class="relative"
                     :class="{'col-span-12':isWhitelabelEnabled(),'col-span-10':!isWhitelabelEnabled()}">
                    <div class="sui-notice sui-notice-info absolute" style="bottom: 30px">
                        <div class="sui-notice-content">
                            <div class="sui-notice-message">
                                <i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
                                <p>
                                    {{__("Use configs to save preset configurations of Defender's settings, then upload and apply them to your other sites in just a few clicks! P.s. save as many of them as you like - you can have unlimited preset configs.")}}
                                </p>
                            </div>
                            <div class="sui-notice-actions">
                                <button class="sui-button-icon" @click="is_shown = 'yes'">
                                    <i class="sui-icon-check" aria-hidden="true"></i>
                                    <span class="sui-screen-reader-text">Close this notice</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sui-accordion sui-accordion-flushed">
            <div @mouseenter="current_config = key; new_config_name = config.name" class="sui-accordion-item"
                 v-for="(config,key) in configs">
                <div class="sui-accordion-item-header">
                    <div class="sui-accordion-item-title sui-accordion-col-3">
                        <span class="defender-container">
                            <i class="sui-icon-defender" aria-hidden="true"></i>
                        </span>
                        <span v-text="config.name"></span>
                        <i v-if="config.immortal" class="sui-icon-check-tick ml-2" aria-hidden="true"></i>
                    </div>
                    <div class="sui-accordion-col-7 config-description flex justify-between">
                        {{__(config.description)}}
                        <button type="button"
                                data-modal-open="apply-config"
                                data-modal-open-focus="configs"
                                data-modal-close-focus="configs"
                                data-modal-mask="false"
                                data-esc-close="true"
                                class="sui-button quick-apply sui-button-ghost sui-accordion-item-action">
                            <i class="sui-icon-check" aria-hidden="true"></i>
                            {{__('Apply')}}
                        </button>
                    </div>
                    <div class="sui-accordion-col-2">
                        <div class="sui-dropdown sui-accordion-item-action">
                            <button class="sui-button-icon sui-dropdown-anchor" aria-label="Dropdown">
                                <i class="sui-icon-more" aria-hidden="true"></i>
                            </button>
                            <ul>
                                <li><a href="#"
                                       data-modal-open="apply-config"
                                       data-modal-open-focus="configs"
                                       data-modal-close-focus="configs"
                                       data-modal-mask="false"
                                       data-esc-close="true"
                                >
                                    <i class="sui-icon-check" aria-hidden="true"></i> {{__('Apply')}}</a></li>
                                <li>
                                    <a :href="download_config_url">
                                        <i class="sui-icon-download" aria-hidden="true"></i> {{__('Download')}}</a>
                                </li>
                                <li v-if="config.immortal === false"><a href=""
                                                                        data-modal-open="rename-config"
                                                                        data-modal-open-focus="configs"
                                                                        data-modal-close-focus="configs"
                                                                        data-modal-mask="false"
                                                                        data-esc-close="true"
                                >
                                    <i class="sui-icon-blog" aria-hidden="true"></i> {{__('Rename')}}</a></li>
                                <li v-if="config.immortal === false"><a href=""
                                                                        data-modal-open="delete-config"
                                                                        data-modal-open-focus="configs"
                                                                        data-modal-close-focus="configs"
                                                                        data-modal-mask="false"
                                                                        data-esc-close="true"
                                >
                                    <i class="sui-icon-trash" aria-hidden="true"></i> {{__('Delete')}}</a>
                                </li>
                            </ul>
                        </div>
                        <button class="sui-button-icon sui-accordion-open-indicator" aria-label="Open item">
                            <i class="sui-icon-chevron-down" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="sui-accordion-item-body">
                    <div class="sui-box">
                        <div class="sui-box-body config-detail">
                            <table class="table-fixed w-full">
                                <tbody>
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{__('Security Recommendations')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.security_tweaks">
                                            <span v-text="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td class="px-4 py-2 font-medium">{{__('Malware Scanning')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.scan">
                                            <span v-html="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="px-4 py-2 font-medium">{{__('Audit Logs')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.audit">
                                            <span v-html="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td class="px-4 py-2 font-medium">{{__('Firewall')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.iplockout">
                                            <span v-html="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="px-4 py-2 font-medium">{{__('Two-Factor Authentication')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.two_factor">
                                            <span v-text="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td class="px-4 py-2 font-medium">{{__('Mask Login Area')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.mask_login">
                                            <span v-text="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{__('Security Headers')}}</td>
                                    <td class="px-4 py-2 font-medium">
                                        <div v-for="item in config.strings.security_headers">
                                            <span v-text="item"></span>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--        <div class="sui-box-body">-->
        <!--            <div class="sui-box-settings-row sui-upsell-row" v-if="is_free == true">-->
        <!--                <div class="sui-upsell-notice no-padding">-->
        <!--                    <p>{{__('Tired of saving, downloading and uploading your configs across your sites? WPMU DEV members use The Hub to easily apply configs to multiple sites at once... Try it free today!')}}<br>-->
        <!--                        <button class="sui-button sui-button-purple" style="margin-top: 10px;">{{__('Try the hub')}}-->
        <!--                        </button>-->
        <!--                    </p>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div v-else>-->
        <!--                <p class="sui-description text-center" v-html="hub_text"></p>-->
        <!--            </div>-->
        <!--        </div>-->
        <div class="sui-modal sui-modal-sm">
            <div
                    role="dialog"
                    id="new-config"
                    class="sui-modal-content"
                    aria-modal="true"
                    aria-labelledby="save-new-config"
                    aria-describedby="save-new-config"
            >
                <div class="sui-box">
                    <div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
                        <button class="sui-button-icon sui-button-float--right" data-modal-close="">
                            <i class="sui-icon-close sui-md" aria-hidden="true"></i>
                            <span class="sui-screen-reader-text">Close this dialog.</span>
                        </button>
                        <h3 class="sui-box-title sui-lg">{{__('Save Current Config')}}</h3>
                        <p class="sui-description">
                            {{__('Save your current Defender settings configuration. You’ll be able to then download and apply it to your other sites with Defender installed.')}}
                        </p>
                    </div>
                    <div class="sui-box-body">
                        <div class="sui-form-field">
                            <label class="sui-label">{{__('Config name')}}</label>
                            <input type="text" v-model="config_name" class="sui-form-control">
                        </div>
                    </div>
                    <div class="sui-box-footer sui-content-right">
                        <button class="sui-button sui-button-ghost" data-modal-close="">
                            Cancel
                        </button>
                        <submit-button @click="new_config" :state="state" css-class="sui-button sui-button-blue"
                                       :disabled="!config_name.length">
                            <i class="sui-icon-save" aria-hidden="true"></i> {{__('Save new')}}
                        </submit-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="sui-modal sui-modal-sm">
            <div
                    role="dialog"
                    id="rename-config"
                    class="sui-modal-content"
                    aria-modal="true"
                    aria-labelledby="rename-config"
                    aria-describedby="rename-config"
            >
                <div class="sui-box">
                    <div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
                        <button class="sui-button-icon sui-button-float--right" data-modal-close="">
                            <i class="sui-icon-close sui-md" aria-hidden="true"></i>
                            <span class="sui-screen-reader-text">Close this dialog.</span>
                        </button>
                        <h3 class="sui-box-title sui-lg">{{__('Rename Config')}}</h3>
                        <p class="sui-description">
                            {{__('Change your config name to something recognizable.')}}
                        </p>
                    </div>
                    <div class="sui-box-body">
                        <div class="sui-form-field">
                            <label class="sui-label">{{__('New config name')}}</label>
                            <input type="text" v-model="new_config_name" class="sui-form-control">
                        </div>
                    </div>
                    <div class="sui-box-footer sui-content-right">
                        <button class="sui-button sui-button-ghost" data-modal-close="">
                            Cancel
                        </button>
                        <submit-button @click="rename_config" :state="state" css-class="sui-button sui-button-blue"
                                       :disabled="!new_config_name.length">
                            <i class="sui-icon-save" aria-hidden="true"></i> {{__('Save')}}
                        </submit-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="sui-modal sui-modal-sm">
            <div
                    role="dialog"
                    id="apply-config"
                    class="sui-modal-content"
                    aria-modal="true"
                    aria-labelledby="apply-config"
                    aria-describedby="apply-config"
            >
                <div class="sui-box">
                    <div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
                        <button class="sui-button-icon sui-button-float--right" data-modal-close="">
                            <i class="sui-icon-close sui-md" aria-hidden="true"></i>
                            <span class="sui-screen-reader-text">Close this dialog.</span>
                        </button>
                        <h3 class="sui-box-title sui-lg">{{__('Apply config')}}</h3>
                        <p class="sui-description" v-html="apply_text">
                        </p>
                    </div>
                    <div class="sui-box-footer sui-flatten sui-content-center">
                        <button class="sui-button sui-button-ghost" data-modal-close="">
                            Cancel
                        </button>
                        <submit-button @click="apply_config" :state="state" css-class="sui-button sui-button-blue"
                                       :disabled="!new_config_name.length">
                            <i class="sui-icon-check" aria-hidden="true"></i> {{__('Apply')}}
                        </submit-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="sui-modal sui-modal-sm">
            <div
                    role="dialog"
                    id="delete-config"
                    class="sui-modal-content"
                    aria-modal="true"
                    aria-labelledby="delete-config"
                    aria-describedby="delete-config"
            >
                <div class="sui-box">
                    <div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
                        <button class="sui-button-icon sui-button-float--right" data-modal-close="">
                            <i class="sui-icon-close sui-md" aria-hidden="true"></i>
                            <span class="sui-screen-reader-text">Close this dialog.</span>
                        </button>
                        <h3 class="sui-box-title sui-lg">{{__('Delete Configuration File')}}</h3>
                        <p class="sui-description" v-html="delete_text">
                        </p>
                    </div>
                    <div class="sui-box-footer sui-flatten sui-content-center">
                        <button class="sui-button sui-button-ghost" data-modal-close="">
                            {{__('Cancel')}}
                        </button>
                        <submit-button css-class="sui-button sui-button-red" :state="state" @click="delete_config">
                            {{__('Delete')}}
                        </submit-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import base_helper from '../../../helper/base_hepler';

    export default {
        mixins: [base_helper],
        name: "configs",
        data: function () {
            return {
                is_free: defender.is_free,
                endpoints: wdSettings.endpoints,
                nonces: wdSettings.nonces,
                configs: wdSettings.configs,
                config_name: '',
                new_config_name: '',
                current_config: '',
                uploading: false,
                file_upload: '',
                state: {
                    on_saving: false
                },
                is_shown: 'no'
            }
        },
        methods: {
            apply_config: function () {
                let self = this;
                this.httpPostRequest('applyConfig', {
                    key: self.current_config
                }, function (response) {
                    if (response.success === true) {
                        if (response.data.login_url !== undefined) {
                            setTimeout(function () {
                                location.href = response.data.login_url;
                            }, 2000)
                        } else {
                            self.configs = response.data.configs
                            self.$nextTick(() => {
                                self.config_name = '';
                                SUI.closeModal()
                            })
                        }
                    }
                })
            },
            new_config: function () {
                let self = this;
                this.httpPostRequest('newConfig', {
                    name: self.config_name
                }, function (response) {
                    if (response.success === true) {
                        self.configs = response.data.configs
                        self.$nextTick(() => {
                            self.rebindSUI()
                            self.config_name = '';
                            SUI.closeModal()
                        })
                    }
                })
            },
            rename_config: function () {
                let self = this;
                this.httpPostRequest('updateConfig', {
                    key: self.current_config,
                    name: self.new_config_name
                }, function (response) {
                    if (response.success === true) {
                        self.configs = response.data.configs
                        self.$nextTick(() => {
                            SUI.closeModal()
                        })
                    }
                })
            },
            delete_config: function () {
                let self = this;
                this.httpPostRequest('deleteConfig', {
                    key: self.current_config
                }, function (response) {
                    if (response.success === true) {
                        self.configs = response.data.configs
                        self.$nextTick(() => {
                            SUI.closeModal()
                        })
                    }
                })
            },
            import_config: function () {
                let file = this.$refs.file.files[0]
                let self = this;
                if (file.type === "application/json") {
                    let formData = new FormData;
                    formData.append('file', file)
                    jQuery.ajax({
                        url: ajaxurl + '?action=' + this.endpoints['importConfig'] + '&_wpnonce=' + this.nonces['importConfig'],
                        type: "POST",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend(jqXHR, settings) {
                            self.state.on_saving = true;
                        },
                        success: function (response) {
                            if (response.success === true) {
                                Defender.showNotification('success', response.data.message)
                                self.configs = response.data.configs
                                self.$nextTick(() => {
                                    self.uploading = false;
                                    self.state.on_saving = false;
                                    jQuery('#json_file').val('')
                                    self.rebindSUI();
                                })
                            } else {
                                self.uploading = false;
                                self.state.on_saving = false;
                                jQuery('#json_file').val('')
                                Defender.showNotification('error', response.data.message)
                            }
                        }
                    })
                }
            },
            file_changed: function (e) {
                let file = this.$refs.file.files[0];
                if (file.type === "application/json") {
                    this.uploading = true;
                    this.file_upload = file.name;
                    this.import_config()
                } else {
                    Defender.showNotification('error', this.__('You uploaded an invalid file. Only JSON file types are allowed. Please try uploading again.'))
                }
            },
            clear_file: function () {
                this.can_upload = false;
                this.file_upload = '';
                jQuery('#json_file').val('')
            }
        },
        computed: {
            download_config_url: function () {
                return ajaxurl + '?action=' + this.endpoints['downloadConfig'] + '&_wpnonce=' + this.nonces['downloadConfig'] + '&key=' + this.current_config;
            },
            config: function () {
                return this.configs[this.current_config]
            },
            hub_text: function () {
                return this.vsprintf(this.__('Did you know you can apply your configs to any connected website in <a href="%s">The Hub</a>'), '')
            },
            apply_text: function () {
                if (this.config !== undefined)
                    return this.vsprintf(this.__('Are you sure you want to apply the <span class="text-gray-500 font-semibold">%s</span> settings config to <span class="text-gray-500 font-semibold">%s</span>? We recommend you have a backup available as your existing settings configuration will be overridden.'), this.config.name, this.siteUrl)
            },
            delete_text: function () {
                if (this.config !== undefined) {
                    return this.vsprintf(this.__('Are you sure you want to delete the <span class="text-gray-500 font-semibold">%s</span> config file? You will no longer be able to apply it to this or other connected sites.'), this.config.name)
                }
            }
        },
        mounted() {
            jQuery('body').on('click', '#open-uploader', function () {
                jQuery('#json_file').trigger('click')
            });
            if (localStorage.is_shown) {
                this.is_shown = localStorage.is_shown
            }

        },
        watch: {
            is_shown: function (newValue) {
                localStorage.is_shown = newValue;
            }
        }
    }
</script>