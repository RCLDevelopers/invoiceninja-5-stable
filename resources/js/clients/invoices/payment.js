/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license 
 */

class Payment {
    constructor(displayTerms, displaySignature, displayRff) {
        this.shouldDisplayTerms = displayTerms;
        this.shouldDisplaySignature = displaySignature;
        this.shouldDisplayRff = displayRff;

        this.submitting = false;
        this.steps = new Map()

        if (this.shouldDisplayRff) {
            this.steps.set("rff", {
                element: document.getElementById('displayRequiredFieldsModal'),
                nextButton: document.getElementById('rff-next-step'),
                callback: () => {
                    const fields = {
                        firstName: document.querySelector('input[name="rff_first_name"]'),
                        lastName: document.querySelector('input[name="rff_last_name"]'),
                        email: document.querySelector('input[name="rff_email"]'),
                    }

                    if (fields.firstName) {
                        document.querySelector('input[name="contact_first_name"]').value = fields.firstName.value;
                    }

                    if (fields.lastName) {
                        document.querySelector('input[name="contact_last_name"]').value = fields.lastName.value;
                    }

                    if (fields.email) {
                        document.querySelector('input[name="contact_email"]').value = fields.email.value;
                    }
                }
            });
        }

        if (this.shouldDisplaySignature) {
            this.steps.set("signature", {
                element: document.getElementById('displaySignatureModal'),
                nextButton: document.getElementById('signature-next-step'),
                boot: () => this.signaturePad = new SignaturePad(
                    document.getElementById("signature-pad"),
                    {
                        penColor: "rgb(0, 0, 0)"
                    }
                ),
                callback: () => document.querySelector('input[name="signature"').value = this.signaturePad.toDataURL(),
            });
        }

        if (this.shouldDisplayTerms) {
            this.steps.set("terms", {
                element: document.getElementById('displayTermsModal'),
                nextButton: document.getElementById('accept-terms-button'),
            });
        }
    }

    handleMethodSelect(element) {

        document.getElementById("company_gateway_id").value =
            element.dataset.companyGatewayId;
        document.getElementById("payment_method_id").value =
            element.dataset.gatewayTypeId;
        
        if (this.steps.size === 0) {
            return this.submitForm();
        }

        const next = this.steps.values().next().value;

        next.element.removeAttribute("style");
        
        if (next.boot) {
            next.boot();
        }

        console.log(next);

        next.nextButton.addEventListener('click', () => {
            next.element.setAttribute("style", "display: none;");

            this.steps = new Map(Array.from(this.steps.entries()).slice(1));

            if (next.callback) {
                next.callback();
            }

            this.handleMethodSelect(element);
        });
    }

    submitForm() {
        this.submitting = true;

        document.getElementById("payment-form").submit();
    }

    handle() {

        document
            .querySelectorAll(".dropdown-gateway-button")
            .forEach(element => {
                element.addEventListener("click", () => {
                    if (!this.submitting) {
                        this.handleMethodSelect(element)
                    }
                });
            });
    }
}

const signature = document.querySelector(
    'meta[name="require-invoice-signature"]'
).content;

const terms = document.querySelector('meta[name="show-invoice-terms"]').content;
const rff = document.querySelector('meta[name="show-required-fields-form"]').content;

new Payment(Boolean(+terms), Boolean(+signature), Boolean(+rff)).handle();
