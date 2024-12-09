<script setup>
import {ref, onMounted, computed} from 'vue';
import axios from "axios";
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const paymentInfo = ref({});
const depositAmount = ref(0);
const paymentMethod = ref({
    'is_wallet_enabled': false,
    'is_gateway_enabled': false,
    'wallet': [],
    'gateway': [],
});
const id = computed(() => route.params.tracking_no);

const payType = ref('BKASH');
const selectedPayment = ref('');
const reg_key = ref('');
const loadingNext = ref(false);
const mfsMessage = ref('');

const getPaymentInfo = async () => {
    const response = await axios.get(`/pilgrim/voucher/get-payment-info`, {
        params: {
            tracking_no: id.value,
        }
    });
    if (response.data.responseCode === 1) {
        paymentInfo.value = response.data.data.data.voucherInfo;
        paymentMethod.value = response.data.data.data.paymentMethod;
        depositAmount.value = response.data.data.data.depositAmount;
    }else{
        alert(response.data.msg);
        router.go(-1);
    }
}
const submitPay = async () => {
    const postData = {
        "tracking_no": paymentInfo.value.tracking_no,
        "payType": payType.value,
        "reg_key": reg_key.value,
    }
    if (!postData.tracking_no || !postData.payType || !postData.reg_key) {
        alert('Invalid Payment Method Info.');
        return false;
    }
    loadingNext.value = true;
    const response = await axios.post(`/pilgrim/voucher/payment-submit`, postData);
    loadingNext.value = false;
    if (response.data.responseCode === 1) {
        const url = response.data.data;
        window.location.href = url;
    } else {
        alert(response.data.msg);
        //router.push({ name: 'voucherList' });
    }
}

const onPaymentMFS = () => {
    selectedPayment.value ='mfs';
    payType.value = '';
    mfsMessage.value = `আপনার ভাউচার নাম্বার : ${paymentInfo.value.tracking_no}।  আপনি এই  ভাউচার নাম্বার দিয়ে মোবাইল অ্যাপ এর মাধ্যমে পেমেন্ট সম্পন্ন করতে পারবেন।`;
}

onMounted(async () => {
    await getPaymentInfo();
});

const onInternetBanking = () => {
    payType.value = '';
    selectedPayment.value = 'internet';
}

const selectPaymentGateway = (methodName, regKey = '') => {
    payType.value = methodName;
    reg_key.value = '';
    if(regKey) {
        reg_key.value = regKey;
    } else {
        reg_key.value = '';
    }
}

</script>
<template>
    <div class="payment-div py-5">
        <div class="hajj-payment-slip">
            <div class="text-center">
                <div>
                    <img
                        src="/assets/custom/images/2024/haj-logo.svg"
                        alt="Hajj logo"
                        style="height: 70px !important"
                    />
                </div>
                <div class="payment-slip-title">
                    <h3>eHajj Payment</h3>
                </div>
            </div>

            <div class="voucher-table">
                <table>
                    <tbody>
                    <tr>
                        <th>Voucher Number</th>
                        <td><strong>{{ paymentInfo.tracking_no}}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Pilgrims</th>
                        <td><strong>{{paymentInfo.total_pilgrim}}</strong></td>
                    </tr>
                    <tr>
                        <th>Deposit Amount</th>
                        <td><strong>{{ depositAmount ? depositAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00' }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="td-border"></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Amount</th>
                        <td><strong>{{ paymentInfo.amount ? paymentInfo.amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00' }}</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="hajj-payment-method">
                <h4>Payment Method</h4>
                <!-- radio type start -->
                <div class="payment-methods">
                    <template v-if="paymentMethod.is_wallet_enabled">
                        <label class="payment-method">
                            <input type="radio" name="payment" value="mfs" @click="onPaymentMFS">
                            <div class="method-content">
                                <img
                                    src="/assets/custom/images/2024/msf.svg"
                                    alt="mfs logo"
                                />
                                <span class="method-name">Mobile Apps</span>
                                <span class="radio-circle"></span>
                            </div>
                        </label>
                    </template>
                    <template v-if="paymentMethod.is_gateway_enabled">
                        <label class="payment-method">
                            <input type="radio" name="payment"  @click="onInternetBanking">
                            <div class="method-content">
                                <img
                                    src="/assets/custom/images/2024/sonali.svg"
                                    alt="sonali logo"
                                />
                                <span class="method-name">Online Gateway</span>
                                <span class="radio-circle"></span>
                            </div>
                        </label>
                    </template>
                    <!-- <label class="payment-method">
                        <input type="radio" name="payment" value="cards">
                        <div class="method-content">
                            <img src="/assets/custom/images/2024/card.svg" alt="cards logo">
                            <span class="method-name">Cards</span>
                            <span class="radio-circle"></span>
                        </div>
                    </label> -->
                </div>
                <!-- radio type end -->
                <div  v-if="selectedPayment === 'mfs'" class="payment-method-lists">
                    <div v-for="wallet in paymentMethod.wallet" :key="wallet.id" class="col-md-4 payment-method-item-mfs">
                        <span class="payment-method-icon">
                            <img
                                :src="wallet.logo_path"
                                :alt="wallet.method_name"
                            />
                        </span>
<!--                            <span class="payment-method-title">Pay with {{ wallet.ui_method_name }}</span>-->
                            <span class="active-payment-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                <path d="M1.58838 5.50014L3.11397 7.46159C3.16545 7.52848 3.23141 7.58285 3.3069 7.62063C3.38239 7.65835 3.46546 7.67852 3.54985 7.67953C3.63289 7.68053 3.71509 7.66293 3.7905 7.62817C3.86589 7.59336 3.93259 7.54217 3.98573 7.47835L8.85308 1.58838" stroke="white" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div v-if="selectedPayment === 'internet'" class="payment-method-lists">
                    <div v-for="gateway in paymentMethod.gateway" :key="gateway.id" class="payment-method-item" :class="{ 'active-payment': payType === gateway.method_name }" @click="selectPaymentGateway(gateway.method_name, gateway.wallet_reg_key)" >
                        <span class="payment-method-icon">
                            <img
                                :src="gateway.logo_path"
                                :alt="gateway.method_name"
                            />
                        </span>
                        <span class="payment-method-title">Pay with {{ gateway.ui_method_name }}</span>
                        <span class="active-payment-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                <path d="M1.58838 5.50014L3.11397 7.46159C3.16545 7.52848 3.23141 7.58285 3.3069 7.62063C3.38239 7.65835 3.46546 7.67852 3.54985 7.67953C3.63289 7.68053 3.71509 7.66293 3.7905 7.62817C3.86589 7.59336 3.93259 7.54217 3.98573 7.47835L8.85308 1.58838" stroke="white" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </div>
               <span class="text-danger"><b>পেমেন্টর সময় সংশিষ্ট সেবা প্রদানকারী আর্থিক প্রতিষ্ঠান অতিরিক্ত  চার্জ গ্রহন করবে।</b></span>
               <template v-if="selectedPayment === 'mfs'">
                   <span style="display: inline-block; margin-top: 20px;" ><b>{{ mfsMessage }} <a :href="paymentMethod.wallet[0]['user_manual']" target="_blank">অ্যাপ থেকে পেমেন্ট করার পদ্ধতি।</a></b></span>
               </template>

                <div v-if="loadingNext" class="pay-btn-group">
                    <button type="button" class="hajj-payment-btn" disabled>
                        Loading...
                    </button>
                </div>
                <div v-if="!loadingNext && selectedPayment === 'internet'" class="pay-btn-group">
                    <button class="hajj-payment-btn" @click="submitPay">Pay {{ paymentInfo.amount ? paymentInfo.amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00' }} BDT</button>
                </div>
                <div class="pay-btn-group">
                    <router-link :to="{name:'voucherList' }" class="hajj-close-payment-btn">Close</router-link>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>

/**
 * Payment Slip
 */
.hajj-payment-slip{
    position: relative;
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    border-radius: 8.837px;
    background: #ffffff;
    text-align: left;
    box-shadow: 0px 1.767px 9.721px -1.767px rgba(0, 0, 0, 0.06);
}
.hajj-payment-slip .payment-slip-title{
    padding-bottom: 5px;
    border-bottom: 1px solid #DFE2E6;
    margin-bottom: 15px;
}
.hajj-payment-slip h4,
.hajj-payment-slip h3{
    font-size: 18px;
    font-weight: 600;
    line-height: normal;
    color: #0A0D13;
    margin: 0 0 5px;
}
.hajj-payment-slip h4{
    font-size: 15px;
}

.td-border{
    display: block;
    width: 100%;
    height: 1px;
    background: #DFE2E6;
}
.voucher-table{
    width: 100%;
    position: relative;
    padding: 10px;
    background: #FAFBFB;
    border-radius: 4px;
    margin-bottom: 15px;
}
.voucher-table table{
    width: 100%;
    margin: 0;
}
.voucher-table table tr td{
    font-size: 14px;
    line-height: 1.5;
    font-weight: 400;
    padding: 5px;
    color: #646774;
    text-align: left;
}
.voucher-table table tr td strong{
    font-weight: 600;
}
.voucher-table table tr td:last-child{
    text-align: right;
}

.hajj-payment-method{
    position: relative;
    width: 100%;
}

.payment-method-lists{
    width: 100%;
    display: flex;
    column-gap: 4px;
    padding: 10px 0;
}
.payment-method-lists .payment-method-item{
    position: relative;
    display: flex;
    width: calc(50% - 5px);
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    row-gap: 8px;
    height: 98px;
    padding: 12px;
    border-radius: 7px;
    border: 1px solid #EAEDF3;
    transition: all 0.3s ease-in-out 0s;
    cursor: pointer;
    box-sizing: border-box;
}

.payment-method-lists .payment-method-item-mfs{
    position: relative;
    display: flex;
    width: calc(50% - 5px);
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    row-gap: 8px;
    height: 98px;
    padding: 12px;
    border-radius: 7px;
    border: 1px solid #EAEDF3;
    transition: all 0.3s ease-in-out 0s;
    cursor: default;
    box-sizing: border-box;
}

.payment-method-lists .payment-method-item.active-payment,
.payment-method-lists .payment-method-item:hover{
    border-color: #28A745;
}
.payment-method-lists .payment-method-item.active-payment .active-payment-icon{
    opacity: 1;
}
.payment-method-lists .payment-method-item .payment-method-icon{
    display: flex;
    height: 40px;
    width: 40px;
    align-items: center;
    justify-content: center;
}
.payment-method-lists .payment-method-item .payment-method-icon img,
.payment-method-lists .payment-method-item .payment-method-icon svg{
    max-height: 40px;
    max-width: 60px;
}
.payment-method-lists .payment-method-item .payment-method-title{
    display: block;
    font-size: 12px;
    color: #677489;
}

.active-payment-icon{
    position: absolute;
    top: -4px;
    right: -4px;
    height: 20px;
    width: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #28A745;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease-in-out 0s;
}
.active-payment-icon svg{
    width: 10px;
}

.pay-btn-group{
    position: relative;
    display: flex;
    width: 100%;
    padding-top: 10px;
}
.hajj-payment-btn{
    height: 42px;
    font-size: 14px;
    line-height: normal;
    display: flex;
    width: 100%;
    background: #28A745;
    border-radius: 7px;
    color: #ffffff;
    border: none !important;
    box-shadow: none !important;
    align-items: center;
    justify-content: center;
    text-align: center;
    transition: all 0.3s ease-in-out 0s;
    cursor: pointer;
}
.hajj-close-payment-btn{
    height: 42px;
    font-size: 14px;
    line-height: normal;
    display: flex;
    width: 100%;
    background: #a7cdc1;
    border-radius: 7px;
    color: #100101;
    border: none !important;
    box-shadow: none !important;
    align-items: center;
    justify-content: center;
    text-align: center;
    transition: all 0.3s ease-in-out 0s;
    cursor: pointer;
}
/**
 * Payment Radio
 */
.payment-methods {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    flex-direction: row;
}

.payment-method {
    display: flex;
    align-items: center;
    padding: 10px 16px;
    border: 2px solid #ced4da;
    border-radius: 6px;
    cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
    background-color: white;
}

.payment-method input {
    display: none;
}

.payment-method .method-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.payment-method .method-icon {
    width: 24px;
    height: 24px;
    background-color: #ced4da;
    border-radius: 3px;
}

.payment-method .method-name {
    font-size: 14px;
    color: #6c757d;
}

.payment-method .radio-circle {
    width: 18px;
    height: 18px;
    border: 2px solid #ced4da;
    border-radius: 50%;
    position: relative;
}

.payment-method .radio-circle::after {
    content: '';
    width: 10px;
    height: 10px;
    background-color: transparent;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    transition: background-color 0.2s;
}

.payment-method input:checked + .method-content .radio-circle {
    border-color: #198754;
}

.payment-method input:checked + .method-content .radio-circle::after {
    border-color: #198754;
    background-color: #198754;
}

.payment-method:hover,
.payment-method input:checked + .method-content {
    border-color: #198754;
    color: #198754;
}

.payment-method input:checked + .method-content .method-icon {
    background-color: #198754;
    border-color: #198754;
}

.payment-method input:checked + .method-content .method-name {
    color: #198754;
    border-color: #198754;
}

.payment-method.disabled {
    border-color: #e9ecef;
    cursor: not-allowed;
}

.payment-method.disabled .method-icon,
.payment-method.disabled .method-name,
.payment-method.disabled .radio-circle {
    color: #adb5bd;
    background-color: #e9ecef;
    border-color: #e9ecef;
}

.payment-method.disabled .radio-circle::after {
    background-color: transparent;
}
.payment-div {
    background-image: url("/assets/custom/images/2024/background-image.svg");
}

</style>
