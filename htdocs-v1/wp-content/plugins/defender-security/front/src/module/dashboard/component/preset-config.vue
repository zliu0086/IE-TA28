<template>
    <div class="sui-box preset-config">
        <div class="sui-box-header">
            <h3 class="sui-box-title">
                <i class="sui-icon-wrench-tool" aria-hidden="true"></i>
                {{__("Preset Configs")}}
            </h3>
            <div class="sui-actions-right">
                <a :href="adminUrl('admin.php?page=wdf-setting&view=configs')" class="sui-button sui-button-ghost">
                    <i class="sui-icon-wrench-tool" aria-hidden="true"></i>
                    {{__('Manage Configs')}}
                </a>
            </div>
        </div>
        <div class="sui-box-body">
            <p>
                {{__('Configs bundle your Defender settings and make them available to download and apply on your other sites.')}}
            </p>
            <div class="sui-field-list sui-flushed no-border-top margin-bottom-30">
                <div class="sui-field-list-body">
                    <div v-for="(config,key) in configs" class="sui-field-list-item"
                         @mouseenter="current_config = key; new_config_name = config.name">
                        <label
                                class="sui-field-list-item-label flex content-center items-center"
                        >
                            <span class="defender-container">
                                <i class="sui-icon-defender" aria-hidden="true"></i>
                            </span>
                            <strong v-text="config.name"></strong>
                            <i v-if="config.immortal" class="sui-icon-check-tick ml-2" aria-hidden="true"></i>
                        </label>
                        <div class="sui-dropdown sui-accordion-item-action">
                            <button class="sui-button-icon sui-dropdown-anchor" aria-label="Dropdown">
                                <i class="sui-icon-more" aria-hidden="true"></i>
                            </button>
                            <ul>
                                <li><a href="#"
                                       data-modal-open="apply-config"
                                       data-modal-open-focus="configs"
                                       data-modal-close-focus="wpwrap"
                                       data-modal-mask="false"
                                       data-esc-close="true"
                                >
                                    <i class="sui-icon-check" aria-hidden="true"></i> {{__('Apply')}}</a></li>
                                <li>
                                    <a :href="download_config_url"
                                    >
                                        <i class="sui-icon-download" aria-hidden="true"></i> {{__('Download')}}</a>
                                </li>
                                <li v-if="config.immortal == false"><a href=""
                                                                       data-modal-open="rename-config"
                                                                       data-modal-open-focus="configs"
                                                                       data-modal-close-focus="wpwrap"
                                                                       data-modal-mask="false"
                                                                       data-esc-close="true"
                                >
                                    <i class="sui-icon-blog" aria-hidden="true"></i> {{__('Rename')}}</a></li>
                                <li v-if="config.immortal == false"><a href=""
                                                                       data-modal-open="delete-config"
                                                                       data-modal-open-focus="configs"
                                                                       data-modal-close-focus="wpwrap"
                                                                       data-modal-mask="false"
                                                                       data-esc-close="true"
                                >
                                    <i class="sui-icon-trash" aria-hidden="true"></i> {{__('Delete')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sui-notice sui-notice-info">
                <div class="sui-notice-content">
                    <div class="sui-notice-message">
                        <i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
                        <p>{{__('Use configs to save preset configurations of Defender\'s settings, then upload and apply them to your other sites in just a few clicks! P.s. save as many of them as you like - you can have unlimited preset configs.')}}</p>
                    </div>
                </div>
            </div>
        </div>
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
                        <p class="sui-description" v-html="delete_text"></p>
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
    import base_helper from '../../../helper/base_hepler'

    export default {
        name: "preset-config",
        mixins: [base_helper],
        data: function () {
            return {
                endpoints: dashboard.settings.endpoints,
                nonces: dashboard.settings.nonces,
                configs: dashboard.settings.configs,
                config_name: '',
                new_config_name: '',
                current_config: '',
                state: {
                    on_saving: false
                }
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
        methods: {
            apply_config: function () {
                let self = this;
                this.httpPostRequest('applyConfig', {
                    key: self.current_config,
                    screen: 'dashboard'
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
        }
    }
</script>