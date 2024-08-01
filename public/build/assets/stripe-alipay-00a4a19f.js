var i=Object.defineProperty;var c=(n,e,t)=>e in n?i(n,e,{enumerable:!0,configurable:!0,writable:!0,value:t}):n[e]=t;var r=(n,e,t)=>(c(n,typeof e!="symbol"?e+"":e,t),t);/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license 
 */class a{constructor(e,t){r(this,"setupStripe",()=>(this.stripeConnect?this.stripe=Stripe(this.key,{stripeAccount:this.stripeConnect}):this.stripe=Stripe(this.key),this));this.key=e,this.stripeConnect=t,this.errors=document.getElementById("errors")}async handle(){document.getElementById("pay-now").addEventListener("click",async e=>{document.getElementById("pay-now").disabled=!0,document.querySelector("#pay-now > svg").classList.add("hidden"),document.querySelector("#pay-now > span").classList.remove("hidden");const{error:t}=await this.stripe.confirmAlipayPayment(document.querySelector("meta[name=ci_intent]").content,{return_url:`${document.querySelector("meta[name=return_url]").content}`});document.getElementById("pay-now").disabled=!1,document.querySelector("#pay-now > svg").classList.remove("hidden"),document.querySelector("#pay-now > span").classList.add("hidden"),t&&(this.errors.textContent="",this.errors.textContent=result.error.message,this.errors.hidden=!1)})}}var s;const d=((s=document.querySelector('meta[name="stripe-publishable-key"]'))==null?void 0:s.content)??"";var o;const l=((o=document.querySelector('meta[name="stripe-account-id"]'))==null?void 0:o.content)??"";new a(d,l).setupStripe().handle();
