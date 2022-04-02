<template>

    <Card main_responsive_classes="col-11 col-sm-6 col-md-11 col-lg-7 col-xl-6 col-xxl-5">

        <template #heading>
            {{ createOrEdit + ' Shop' }}
        </template>

        <template #cardBody>

            <Input v-model="form.fields.name" :err="form.errors.name"
                   maxlength="50" required>
                {{ $t('message.shop_name') }}
            </Input>


            <div class="text-center" v-show="show_retry_btn">
                <Button btn_txt="RELOAD" classes="btn-info" @click="getTimeZones" :disabled="show_loader"
                        :show_spinner="show_loader"/>
            </div>

            <Dropdown v-model="form.fields.time_zone_id" :err="form.errors.time_zone_id" v-show="!show_retry_btn"
                      :options="time_zones">
                {{ $t('message.time_zone') }}
            </Dropdown>

            <TextArea v-model="form.fields.address"
                      :err="form.errors.address" required
                      @keyup.enter="saveShop">
                {{$t('message.shop_address')}}
            </TextArea>

            <Input v-model="form.fields.city" :err="form.errors.city"
                   maxlength="50"
                   required>
                {{ $t('message.city') }}
            </Input>

            <phone-number @updateDialingCode="form.fields.dialing_code=$event"
                          :initial_dialing_code="shop?.dialing_code ?? ''"
                          :initial_contact_no="shop?.contact_number ?? ''"
                          :label="$t('message.contact_number')" @updateMobile="form.fields.contact_number=$event"
                          :dialing_code_err="form.errors.dialing_code" :mobile_err="form.errors.contact_number"/>

            <div class="bg-light fw-500 mb-3 p-2">{{ $t('message.print_settings') }}</div>

            <div class="row mb-3">

                <div class="col-6">
                    <Checkbox2 v-model:checked="form.fields.print_on_sale">
                        {{ $t('message.print_on_sale') }}
                    </Checkbox2>
                </div>

                <div class="col-6">
                    <Checkbox2
                        v-model:checked="form.fields.print_on_purchase">
                        {{ $t('message.print_on_purchase') }}
                    </Checkbox2>
                </div>

            </div>

            <div class="row">

                <div class="col-6">

                    <Input v-model="form.fields.printer_name"
                           :err="form.errors.printer_name" maxlength="50">
                        {{ $t('message.printer_name') }}
                    </Input>
                </div>

                <div class="col-6">
                    <Dropdown :options="operating_systems" v-model="form.fields.operating_system"
                              :err="form.errors.operating_system">
                        {{ $t('message.operating_system') }}
                    </Dropdown>
                </div>

            </div>

        </template>

        <template #cardButtons>

            <div class="row">
                <div class="col text-end">
                    <Button :btn_txt="$t('message.save')" classes="btn-primary" :show_spinner="form_submitted"
                            :disabled="form_submitted"
                            @click="saveShop"/>
                </div>
            </div>

        </template>

    </Card>

</template>

<script>
import Input from "../components/Input";
import Button from "../components/Button";
import Dropdown from "../components/Dropdown";
import TextArea from "../components/TextArea";
import Card from "../components/Card";
import Generic from "../Mixins/Generic";
import PostOrPutRequest from "../Mixins/PostOrPutRequest";
import {getPathNamePart} from "../helpers";
import Checkbox2 from "../components/Checkbox2";
import PhoneNumber from "../components/PhoneNumber";
import GetRequest from "../Mixins/GetRequest";

export default {

    name: "Shop",

    components: {PhoneNumber, Checkbox2, Card, TextArea, Dropdown, Button, Input},

    mixins: [Generic, PostOrPutRequest, GetRequest],

    props: ['shop'],

    data() {

        return {

            post_or_put_request_url: this.shop ? `shops/${getPathNamePart(3)}` : 'shops',

            is_edit: !!this.shop,

            time_zones: [],

            operating_systems: [
                {id: 'Windows', name: 'Windows'},
                {id: 'Mac', name: 'Mac'},
                {id: 'Linux', name: 'Linux'},
                {id: 'Unix', name: 'Unix'},
            ],

            form: {

                fields: {
                    name: this.shop?.name ?? '', time_zone_id: this.shop?.time_zone_id ?? '',
                    city: this.shop?.city ?? '', address: this.shop?.address ?? '',
                    dialing_code: '', contact_number: '',
                    //print_on_sale: this.shop?.print_on_sale ?? null,
                    //print_on_purchase: this.shop?.print_on_purchase ?? null,
                    print_on_sale: this.shop ? (this.shop.print_on_sale > 0 ? true : false) : false,
                    print_on_purchase: this.shop ? (this.shop.print_on_purchase > 0 ? true : false) : false,
                    printer_name: this.shop?.printer_name ?? '',
                    operating_system: this.shop?.operating_system ?? '',
                },

                errors: {
                    name: '', time_zone_id: '', country: '', address: '', print_on_sale: '', print_on_purchase: '',
                    operating_system: '', printer_name: '', dialing_code: '', contact_number: ''
                }
            },

            show_retry_btn: false,
            show_loader: false

        }

    },

    methods: {

        saveShop() {

            this.sendPostOrPutRequest((response) => {
                localStorage.setItem('logged_in_user_shops', JSON.stringify(response.data.data.shops));
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });

        },

        addFormValidations() {
            this.validators.push({field_name: 'name', field_rules: ['required', 'max:50'], display_name: 'Name'});
            this.validators.push({field_name: 'time_zone_id', field_rules: ['required'], display_name: 'Time Zone'});
            this.validators.push({field_name: 'city', field_rules: ['required'], display_name: 'City'});
            this.validators.push({
                field_name: 'address',
                field_rules: ['required', 'max:255'],
                display_name: 'Address'
            });
            this.validators.push({
                field_name: 'contact_number',
                field_rules: ['max:255'],
                display_name: 'Contact Number'
            });

            // following two rules would be dynamically enabled/disabled based on 'print_on_sale' and 'print_on_purchase' values
            this.validators.push({
                field_name: 'printer_name', field_rules: ['required', 'max:255'],
                display_name: 'Printer Name', apply_rules: false
            });

            this.validators.push({
                field_name: 'operating_system', field_rules: ['required'], display_name: 'Operating System',
                apply_rules: false
            });
        },

        getTimeZones() {

            this.show_retry_btn = true;
            this.show_loader = true;

            this.sendGetRequest('time-zones', '',

                () => {
                    this.show_loader = false;
                },

                (response) => {
                    this.time_zones = response.data.data;
                    this.show_retry_btn = false;
                })

        }
    },

    created() {
        this.getTimeZones();
        this.addFormValidations();
    },

    watch: {

        'form.fields.print_on_sale': function (newVal, oldVal) {
            this.enableDisableValidationRule('printer_name', newVal);
            this.enableDisableValidationRule('operating_system', newVal);
        },

        'form.fields.print_on_purchase': function (newVal, oldVal) {
            this.enableDisableValidationRule('printer_name', newVal);
            this.enableDisableValidationRule('operating_system', newVal);
        },

    }

}
</script>
