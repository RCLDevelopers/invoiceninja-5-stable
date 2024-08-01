@component('email.template.client', ['logo' => $logo, 'settings' => $settings, 'company' => $company])
    <div class="center">
        <h1>{{ ctrans('texts.ach_verification_notification_label') }}</h1>
        <p>{{ ctrans('texts.ach_verification_notification') }}</p>

        <div>
            <!--[if (gte mso 9)|(IE)]>
                <table align="center" cellspacing="0" cellpadding="0" style="width: 600px;">
                    <tr>
                        <td align="center" valign="top">
                            <![endif]-->        
                            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" >
                                <tbody>
                                    <tr>
                                    <td align="center" class="new_button" style="border-radius: 2px; background-color: '.$this->settings->primary_color.'">
                                        <a href="{{ $url }}" target="_blank" class="new_button" style="text-decoration: none; border: 1px solid '.$this->settings->primary_color.'; display: inline-block; border-radius: 2px; padding-top: 15px; padding-bottom: 15px; padding-left: 25px; padding-right: 25px; font-size: 20px; color: #fff">
                                        <singleline label="cta button">{{ ctrans('texts.complete_verification') }}</singleline>
                                        </a>
                                    </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                        </td>
                    </tr>
                </table>
            <![endif]-->
        </div>

    </div>
@endcomponent
