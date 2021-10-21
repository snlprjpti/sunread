<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                "name" => "Header",
                "subject" => "Header",
                "email_template_code" => "header",
                "content" => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Sailracing</title>


</head>

<body style="-moz-box-sizing: border-box; -ms-text-size-adjust: 100%; -webkit-box-sizing: border-box; -webkit-text-size-adjust: 100%; Margin: 0; box-sizing: border-box; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
  <style>
    @media only screen {
      html {
        min-height: 100%;
        background: #121212;
      }
    }

    @media only screen and (max-width: 596px) {
      .small-float-center {
        margin: 0 auto !important;
        float: none !important;
        text-align: center !important;
      }
      .small-text-center {
        text-align: center !important;
      }
      .small-text-left {
        text-align: left !important;
      }
      .small-text-right {
        text-align: right !important;
      }
    }

    @media only screen and (max-width: 596px) {
      .hide-for-large {
        display: block !important;
        width: auto !important;
        overflow: visible !important;
        max-height: none !important;
        font-size: inherit !important;
        line-height: inherit !important;
      }
    }

    @media only screen and (max-width: 596px) {
      table.body table.container .hide-for-large,
      table.body table.container .row.hide-for-large {
        display: table !important;
        width: 100% !important;
      }
    }

    @media only screen and (max-width: 596px) {
      table.body table.container .callout-inner.hide-for-large {
        display: table-cell !important;
        width: 100% !important;
      }
    }

    @media only screen and (max-width: 596px) {
      table.body table.container .show-for-large {
        display: none !important;
        width: 0;
        mso-hide: all;
        overflow: hidden;
      }
    }

    @media only screen and (max-width: 596px) {
      table.body img {
        width: auto;
        height: auto;
      }
      table.body center {
        min-width: 0 !important;
      }
      table.body .container {
        width: 95% !important;
      }
      table.body .columns,
      table.body .column {
        height: auto !important;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        padding-left: 16px !important;
        padding-right: 16px !important;
      }
      table.body .columns .column,
      table.body .columns .columns,
      table.body .column .column,
      table.body .column .columns {
        padding-left: 0 !important;
        padding-right: 0 !important;
      }
      table.body .collapse .columns,
      table.body .collapse .column {
        padding-left: 0 !important;
        padding-right: 0 !important;
      }
      td.small-1,
      th.small-1 {
        display: inline-block !important;
        width: 8.33333% !important;
      }
      td.small-2,
      th.small-2 {
        display: inline-block !important;
        width: 16.66667% !important;
      }
      td.small-3,
      th.small-3 {
        display: inline-block !important;
        width: 25% !important;
      }
      td.small-4,
      th.small-4 {
        display: inline-block !important;
        width: 33.33333% !important;
      }
      td.small-5,
      th.small-5 {
        display: inline-block !important;
        width: 41.66667% !important;
      }
      td.small-6,
      th.small-6 {
        display: inline-block !important;
        width: 50% !important;
      }
      td.small-7,
      th.small-7 {
        display: inline-block !important;
        width: 58.33333% !important;
      }
      td.small-8,
      th.small-8 {
        display: inline-block !important;
        width: 66.66667% !important;
      }
      td.small-9,
      th.small-9 {
        display: inline-block !important;
        width: 75% !important;
      }
      td.small-10,
      th.small-10 {
        display: inline-block !important;
        width: 83.33333% !important;
      }
      td.small-11,
      th.small-11 {
        display: inline-block !important;
        width: 91.66667% !important;
      }
      td.small-12,
      th.small-12 {
        display: inline-block !important;
        width: 100% !important;
      }
      .columns td.small-12,
      .column td.small-12,
      .columns th.small-12,
      .column th.small-12 {
        display: block !important;
        width: 100% !important;
      }
      table.body td.small-offset-1,
      table.body th.small-offset-1 {
        margin-left: 8.33333% !important;
        Margin-left: 8.33333% !important;
      }
      table.body td.small-offset-2,
      table.body th.small-offset-2 {
        margin-left: 16.66667% !important;
        Margin-left: 16.66667% !important;
      }
      table.body td.small-offset-3,
      table.body th.small-offset-3 {
        margin-left: 25% !important;
        Margin-left: 25% !important;
      }
      table.body td.small-offset-4,
      table.body th.small-offset-4 {
        margin-left: 33.33333% !important;
        Margin-left: 33.33333% !important;
      }
      table.body td.small-offset-5,
      table.body th.small-offset-5 {
        margin-left: 41.66667% !important;
        Margin-left: 41.66667% !important;
      }
      table.body td.small-offset-6,
      table.body th.small-offset-6 {
        margin-left: 50% !important;
        Margin-left: 50% !important;
      }
      table.body td.small-offset-7,
      table.body th.small-offset-7 {
        margin-left: 58.33333% !important;
        Margin-left: 58.33333% !important;
      }
      table.body td.small-offset-8,
      table.body th.small-offset-8 {
        margin-left: 66.66667% !important;
        Margin-left: 66.66667% !important;
      }
      table.body td.small-offset-9,
      table.body th.small-offset-9 {
        margin-left: 75% !important;
        Margin-left: 75% !important;
      }
      table.body td.small-offset-10,
      table.body th.small-offset-10 {
        margin-left: 83.33333% !important;
        Margin-left: 83.33333% !important;
      }
      table.body td.small-offset-11,
      table.body th.small-offset-11 {
        margin-left: 91.66667% !important;
        Margin-left: 91.66667% !important;
      }
      table.body table.columns td.expander,
      table.body table.columns th.expander {
        display: none !important;
      }
      table.body .right-text-pad,
      table.body .text-pad-right {
        padding-left: 10px !important;
      }
      table.body .left-text-pad,
      table.body .text-pad-left {
        padding-right: 10px !important;
      }
      table.menu {
        width: 100% !important;
      }
      table.menu td,
      table.menu th {
        width: auto !important;
        display: inline-block !important;
      }
      table.menu.vertical td,
      table.menu.vertical th,
      table.menu.small-vertical td,
      table.menu.small-vertical th {
        display: block !important;
      }
      table.menu[align="center"] {
        width: auto !important;
      }
      table.button.small-expand,
      table.button.small-expanded {
        width: 100% !important;
      }
      table.button.small-expand table,
      table.button.small-expanded table {
        width: 100%;
      }
      table.button.small-expand table a,
      table.button.small-expanded table a {
        text-align: center !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
      }
      table.button.small-expand center,
      table.button.small-expanded center {
        min-width: 0;
      }
    }

    @media only screen and (max-width: 596px) {
      th.callout-inner {
        padding: 30px 20px !important;
      }
      table.menu.upper td.menu-item,
      table.menu.upper th.menu-item {
        padding: 20px 20px !important;
      }
      table.button.large table a {
        padding: 10px 20px !important;
      }
      table.product-info {
        margin-top: 0 !important;
      }
      table.order-details tbody tr td {
        font-size: 13px !important;
      }
      th.callout-inner.callout-inner-alt h2,
      th.callout-inner.callout-inner-alt p {
        padding: 0 !important;
      }
      table.order-details tbody tr td:first-child {
        padding-left: 0 !important;
      }
      table.product-attributes tbody tr td:last-child {
        padding-right: 0 !important;
      }
      table.order-details tbody tr td.product-name,
      table.order-details tbody tr td.product-attributes-wrapper {
        padding-top: 10px !important;
      }
      table.totals tbody tr td:first-child {
        padding-left: 10px !important;
      }
      table.totals tbody tr td:last-child {
        padding-right: 10px !important;
      }
      table.totals tbody tr.currency td span.custom-divider,
      table.totals tbody tr.currency td span.payment-method {
        margin-right: 10px !important;
      }
      table.totals tbody tr.total td {
        font-size: 18px !important;
      }
      .text-bg {
        font-size: 22px !important;
      }
      h1 {
        font-size: 35px !important;
      }
      h2 {
        font-size: 24px !important;
      }
    }
  </style>
  <table class="body" data-made-with-foundation="" style="Margin: 0; background: #121212; border-collapse: collapse; border-spacing: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; height: 100%; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
    <tbody>
    <tbody>
      <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="float-center" align="center" valign="top" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0 auto; border-collapse: collapse !important; color: #fff; float: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; word-wrap: break-word;">
          <center data-parsed="" style="min-width: 580px; width: 100%;">
            <table align="center" class="wrapper header float-center" style="Margin: 0 auto; background: #121212; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 100%;">
              <tbody>
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                  <td class="wrapper-inner" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 50px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                    <table align="center" class="container" style="Margin: 0 auto; background: #121212; border-collapse: collapse; border-spacing: 0; margin: 0 auto; padding: 0; text-align: inherit; vertical-align: top; width: 700px;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 0; padding-left: 0; padding-right: 0; text-align: left; width: 588px;">
                                    <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <th style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;">
                                          <center data-parsed="" style="min-width: 532px; width: 100%;">
                                            <img src="{{$store_email_logo_url}}" style="-ms-interpolation-mode: bicubic; clear: both; display: block; margin: 0 auto; max-width: 100%; outline: none; text-decoration: none; width: 180px;max-width: 180px;">
                                          </center>
                                            </th>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </th>

                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table align="center" class="container float-center" style="Margin: 0 auto; background: #121212; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 700px;">
              <tbody>
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Footer",
                "subject" => "Footer",
                "email_template_code" => "footer",
                "content" => '
                    <center data-parsed="" style="min-width: 580px; width: 100%;">
                      <table align="center" class="menu float-center upper" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: auto !important;">
                        <tbody>
                          <tr style="padding: 0; text-align: left; vertical-align: top;">
                            <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                              <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                                <tbody>
                                  <tr style="padding: 0; text-align: left; vertical-align: top;">
                                    <th class="menu-item float-center" style="Margin: 0 auto; color: #fff; float: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 20px 50px; padding-right: 10px; text-align: center;"><a href="#" class="active" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none; text-transform: uppercase;">men</a></th>
                                    <th class="menu-item float-center" style="Margin: 0 auto; color: #fff; float: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 20px 50px; padding-right: 10px; text-align: center;"><a href="#" style="Margin: 0; color: #6C6C6C; font-family: Helvetica, Arial, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none; text-transform: uppercase;">women</a></th>
                                    <th class="menu-item float-center" style="Margin: 0 auto; color: #fff; float: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 20px 50px; padding-right: 10px; text-align: center;"><a href="#" style="Margin: 0; color: #6C6C6C; font-family: Helvetica, Arial, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none; text-transform: uppercase;">junior</a></th>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table align="center" class="menu float-center lower" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; margin-bottom: 0; padding: 0; text-align: center; vertical-align: top; width: auto !important;">
                        <tbody>
                          <tr style="padding: 0; text-align: left; vertical-align: top;">
                            <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                              <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                                <tbody>
                                  <tr style="padding: 0; text-align: left; vertical-align: top;">
                                    <th class="menu-item float-center" style="Margin: 0 auto; color: #fff; float: none; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 10px; padding-right: 10px; text-align: center;"><a href="#" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: underline;">View in browser <span class="arrow" style="font-size: 14px;">→</span> </a></th>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>
                    </center>
                  </td>
                </tr>
              </tbody>
            </table>
          </center>
        </td>
      </tr>
    </tbody>
  </table>


</body>

</html>',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Welcome",
                "subject" => "Welcome",
                "email_template_code" => "welcome_email",
                "content" => '{{hc_include_email_template("header")}}
                 <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 16px; padding-right: 16px; text-align: left; width: 564px;">
                            <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <th style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;">
                                    <h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Helvetica, Arial, sans-serif; font-size: 45px; font-weight: bold; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0 20px; text-align: center; text-transform: uppercase; word-wrap: normal;">Your <br>Account</h1>
                                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <th class="callout-inner" style="Margin: 0; background: #fff; border: none; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 60px; text-align: left; width: 100%;">
                                            <h2 style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">{{$customer_name}},</h2>
                                            <br style="color: #000;">
                                            <h2 style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">We are happy to welcome you to Sailracing community.</h2>
                                            <br style="color: #000;">
                                            <p style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Your account has been created with the provided email. Please click on the link below to activate your account and set a new password</p>

                                            <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="30px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; hyphens: auto; line-height: 30px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>

                                            <table class="button large secondary" style="Margin: 30px 0 0; border-collapse: collapse; border-spacing: 0; color: #000; margin: 0; padding: 0; text-align: left; vertical-align: top; width: auto;">
                                              <tbody>
                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                    <table style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                                      <tbody>
                                                        <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                          <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #121212; border: 0px solid #121212; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0 30px; text-align: left; vertical-align: top; word-wrap: break-word;"><a href="#" style="Margin: 0; border: 0 solid #121212; border-radius: 3px; color: #fff; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.3; margin: 0; padding: 10px 0px 10px 0px; text-align: left; text-decoration: none;">Activate account</a></td>
                                                        </tr>
                                                      </tbody>
                                                    </table>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </th>
                                          <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </th>
                                  <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                </tr>
                              </tbody>
                            </table>
                          </th>
                        </tr>
                      </tbody>
                    </table>
                    {{hc_include_email_template("footer")}}
                ',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Forgot Password",
                "subject" => "Forgot Password",
                "email_template_code" => "forgot_password",
                "content" => '{{hc_include_email_template("header")}}
                 <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 16px; padding-right: 16px; text-align: left; width: 564px;">
                            <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <th style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;">
                                    <h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Helvetica, Arial, sans-serif; font-size: 45px; font-weight: bold; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0 20px; text-align: center; text-transform: uppercase; word-wrap: normal;">Forgot <br>Password</h1>
                                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <th class="callout-inner" style="Margin: 0; background: #fff; border: none; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 60px; text-align: left; width: 100%;">
                                            <h2 style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">{{$customer_name}},</h2>
                                            <br style="color: #000;">
                                            <h2 style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">We are happy to welcome you to Sailracing community.</h2>
                                            <br style="color: #000;">
                                            <p style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">Please click on the link below to reset your password</p>

                                            <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="30px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; hyphens: auto; line-height: 30px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>

                                            <table class="button large secondary" style="Margin: 30px 0 0; border-collapse: collapse; border-spacing: 0; color: #000; margin: 0; padding: 0; text-align: left; vertical-align: top; width: auto;">
                                              <tbody>
                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                    <table style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                                      <tbody>
                                                        <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                          <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #121212; border: 0px solid #121212; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0 30px; text-align: left; vertical-align: top; word-wrap: break-word;"><a href="#" style="Margin: 0; border: 0 solid #121212; border-radius: 3px; color: #fff; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: normal; line-height: 1.3; margin: 0; padding: 10px 0px 10px 0px; text-align: left; text-decoration: none;">Reset Password</a></td>
                                                        </tr>
                                                      </tbody>
                                                    </table>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </th>
                                          <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                        </tr>
                                      </tbody>
                                    </table>


                                  </th>
                                  <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                </tr>
                              </tbody>
                            </table>
                          </th>
                        </tr>
                      </tbody>
                    </table>
                    {{hc_include_email_template("footer")}}
                    ',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Reset Password",
                "subject" => "Reset Password",
                "email_template_code" => "reset_password",
                "content" => "<h2>Reset Password</h2>",
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Contact Form",
                "subject" => "Contact Form",
                "email_template_code" => "contact_form",
                "content" => '
                    {{hc_include_email_template("header")}}
                      <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 16px; padding-right: 16px; text-align: left; width: 564px;">
                            <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <th style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;">
                                    <h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Helvetica, Arial, sans-serif; font-size: 45px; font-weight: bold; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0 20px; text-align: center; text-transform: uppercase; word-wrap: normal;">Thank you for contacting us</h1>
                                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <p class="text-bg" style="Margin: 0; Margin-bottom: 10px; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 32px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; margin-top: 0; padding: 0; text-align: center;">Thank you for contacting us. We will get back to you in 24 hours.</p>

                                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>

                                          </th>
                                          <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                        </tr>
                                      </tbody>
                                    </table>


                                  </th>
                                  <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                </tr>
                              </tbody>
                            </table>
                    {{hc_include_email_template("footer")}}
                ',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Order Confirmation",
                "subject" => "Order Confirmation",
                "email_template_code" => "new_order",
                "content" => '
                    {{hc_include_email_template("header")}}
                          <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                      <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 16px; padding-right: 16px; text-align: left; width: 564px;">
                            <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <th style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;">
                                    <h1 class="text-center" style="Margin: 0; Margin-bottom: 10px; color: inherit; font-family: Helvetica, Arial, sans-serif; font-size: 45px; font-weight: bold; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0 20px; text-align: center; text-transform: uppercase; word-wrap: normal;">Order Confirmation</h1>
                                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                        </tr>
                                      </tbody>
                                    </table>

                                    <table class="callout" style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                      <tbody>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                          <th class="callout-inner callout-inner-alt" style="Margin: 0; background: #fff; border: none; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 60px 40px; text-align: left; width: 100%;">
                                            <h2 style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; padding: 0 20px; text-align: left; word-wrap: normal;">Pierre,</h2>
                                            <br style="color: #000;">
                                            <h2 style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 0; padding: 0 20px; text-align: left; word-wrap: normal;">Thank you for your purchase. Please find your order attached below.</h2>
                                            <br style="color: #000;">
                                            <p style="Margin: 0; Margin-bottom: 10px; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0 20px; text-align: left;">Your order is confirmed and packing will start shortly. You will get a update from us once your order has been packed and shipped.</p>
                                            <table class="spacer" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                              <tbody style="color: #000;">
                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                                </tr>
                                              </tbody>
                                            </table>

                                            <table class="order-details" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                              <tbody style="color: #000;">
                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; padding-left: 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                    <img src="https://i.ibb.co/qyf2Rgh/product.jpg" style="-ms-interpolation-mode: bicubic; clear: both; color: #000; display: block; max-width: 100%; min-width: 60px; outline: none; text-decoration: none; width: auto;">
                                                  </td>
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                    <table class="product-info" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                                      <tbody style="color: #000;">
                                                        <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                          <td class="product-name" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; padding-left: 5px; padding-top: 40px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                            <span style="color: #000;">1x - Orca Rashguard SS Carbon Iwth longer title</span>
                                                          </td>
                                                          <td class="product-attributes-wrapper" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; padding-right: 0; padding-top: 40px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                            <table class="product-attributes" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                                              <tbody style="color: #000;">
                                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 5px; padding-left: 5px; padding-top: 0; text-align: left; vertical-align: middle; word-wrap: break-word;">

                                                                    Size</td>
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 20px 2px 5px; padding-right: 0; padding-top: 0; text-align: right; vertical-align: middle; white-space: nowrap; word-wrap: break-word;">

                                                                    L</td>
                                                                </tr>
                                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 5px; padding-left: 5px; text-align: left; vertical-align: middle; word-wrap: break-word;">Color</td>
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 20px 2px 5px; padding-right: 0; text-align: right; vertical-align: middle; white-space: nowrap; word-wrap: break-word;">BLACK</td>
                                                                </tr>
                                                                <tr class="price" style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 5px; padding-left: 5px; padding-top: 30px; text-align: left; vertical-align: middle; word-wrap: break-word;">Price</td>
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 20px 2px 5px; padding-right: 0; padding-top: 30px; text-align: right; vertical-align: middle; white-space: nowrap; word-wrap: break-word;">1.400 SEK</td>
                                                                </tr>
                                                              </tbody>
                                                            </table>
                                                          </td>
                                                        </tr>
                                                      </tbody>
                                                    </table>
                                                  </td>
                                                </tr>
                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; padding-left: 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                    <img src="https://i.ibb.co/qyf2Rgh/product.jpg" style="-ms-interpolation-mode: bicubic; clear: both; color: #000; display: block; max-width: 100%; min-width: 60px; outline: none; text-decoration: none; width: auto;">
                                                  </td>
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                    <table class="product-info" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                                      <tbody style="color: #000;">
                                                        <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                          <td class="product-name" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; padding-left: 5px; padding-top: 40px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                            <span style="color: #000;">1x - Orca Rashguard SS Carbon Iwth longer title</span>
                                                          </td>
                                                          <td class="product-attributes-wrapper" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 10px 5px; padding-right: 0; padding-top: 40px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                            <table class="product-attributes" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                                              <tbody style="color: #000;">
                                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 5px; padding-left: 5px; padding-top: 0; text-align: left; vertical-align: middle; word-wrap: break-word;">

                                                                    Size</td>
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 20px 2px 5px; padding-right: 0; padding-top: 0; text-align: right; vertical-align: middle; white-space: nowrap; word-wrap: break-word;">

                                                                    L</td>
                                                                </tr>
                                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 5px; padding-left: 5px; text-align: left; vertical-align: middle; word-wrap: break-word;">Color</td>
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 20px 2px 5px; padding-right: 0; text-align: right; vertical-align: middle; white-space: nowrap; word-wrap: break-word;">BLACK</td>
                                                                </tr>
                                                                <tr class="price" style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 5px; padding-left: 5px; padding-top: 30px; text-align: left; vertical-align: middle; word-wrap: break-word;">Price</td>
                                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 2px 20px 2px 5px; padding-right: 0; padding-top: 30px; text-align: right; vertical-align: middle; white-space: nowrap; word-wrap: break-word;">1.400 SEK</td>
                                                                </tr>
                                                              </tbody>
                                                            </table>
                                                          </td>
                                                        </tr>
                                                      </tbody>
                                                    </table>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>

                                            <table class="spacer" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                              <tbody style="color: #000;">
                                                <tr style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td height="50px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 50px; font-weight: normal; hyphens: auto; line-height: 50px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&nbsp;</td>
                                                </tr>
                                              </tbody>
                                            </table>



                                            <table class="totals" style="border-collapse: collapse; border-spacing: 0; color: #000; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                              <tbody style="color: #000;">
                                                <tr class="total" style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-bottom: 10px; padding-left: 20px; padding-top: 20px; text-align: left; text-transform: uppercase; vertical-align: top; word-wrap: break-word;">total</td>
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-bottom: 10px; padding-right: 20px; padding-top: 20px; text-align: right; text-transform: uppercase; vertical-align: top; word-wrap: break-word;">2.800 SEK</td>
                                                </tr>
                                                <tr class="tax" style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-left: 20px; text-align: left; vertical-align: top; word-wrap: break-word;">Included TAX</td>
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-right: 20px; text-align: right; vertical-align: top; word-wrap: break-word;">280 SEK</td>
                                                </tr>
                                                <tr class="total-items" style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-bottom: 10px; padding-left: 20px; text-align: left; vertical-align: top; word-wrap: break-word;">Total Items</td>
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; color: #AEAEAE; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-bottom: 10px; padding-right: 20px; text-align: right; vertical-align: top; word-wrap: break-word;">13 PCS</td>
                                                </tr>
                                                <tr class="currency" style="color: #000; padding: 0; text-align: left; vertical-align: top;">
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; border-top: 1px solid #121212; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-bottom: 30px; padding-left: 20px; padding-top: 10px; text-align: left; vertical-align: top; word-wrap: break-word;">Currency</td>
                                                  <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background-color: #f6f6f6; border-collapse: collapse !important; border-top: 1px solid #121212; color: #000; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 5px 10px; padding-bottom: 30px; padding-right: 20px; padding-top: 10px; text-align: right; vertical-align: top; word-wrap: break-word;">
                                                    <span class="payment-method" style="color: #AEAEAE; font-size: 10px; margin-right: 15px; text-transform: uppercase;">paid with klarna</span> <span class="custom-divider" style="color: #AEAEAE; margin-right: 12px;">|</span>                                                    SEK <span class="flag" style="color: #000; margin-left: 10px;"><img src="https://i.ibb.co/Lnxm0cW/swe-flag.png" style="-ms-interpolation-mode: bicubic; clear: both; color: #000; display: inline-block; max-width: 100%; outline: none; text-decoration: none; width: 16px;"></span>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>



                                          </th>
                                          <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                        </tr>
                                      </tbody>
                                    </table>


                                  </th>
                                  <th class="expander" style="Margin: 0; color: #fff; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
                                </tr>
                              </tbody>
                            </table>
                          </th>
                        </tr>
                      </tbody>
                    </table>
                    {{hc_include_email_template("footer")}}
                ',
                "style" => "",
                "is_system_defined" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
        ];

        DB::table("email_templates")->insert($templates);
    }
}
