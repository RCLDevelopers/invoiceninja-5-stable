@extends('portal.ninja2020.layout.app')
@section('meta_title', ctrans('texts.account_management'))

@section('body')

@push('head')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');

*,
::before,
::after {
  box-sizing: border-box;
  /* 1 */
  border-width: 0;
  /* 2 */
  border-style: solid;
  /* 2 */
  border-color: currentColor;
  /* 2 */
}

::before,
::after {
  --tw-content: '';
}

/*
1. Use a consistent sensible line-height in all browsers.
2. Prevent adjustments of font size after orientation changes in iOS.
3. Use a more readable tab size.
4. Use the user's configured `sans` font-family by default.
*/

html {
  line-height: 1.5;
  /* 1 */
  -webkit-text-size-adjust: 100%;
  /* 2 */
  -moz-tab-size: 4;
  /* 3 */
  -o-tab-size: 4;
     tab-size: 4;
  /* 3 */
  font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  /* 4 */
}

/*
1. Remove the margin in all browsers.
2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
*/

body {
  margin: 0;
  /* 1 */
  line-height: inherit;
  /* 2 */
}

/*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/

hr {
  height: 0;
  /* 1 */
  color: inherit;
  /* 2 */
  border-top-width: 1px;
  /* 3 */
}

/*
Add the correct text decoration in Chrome, Edge, and Safari.
*/

abbr:where([title]) {
  -webkit-text-decoration: underline dotted;
          text-decoration: underline dotted;
}

/*
Remove the default font size and weight for headings.
*/

h1,
h2,
h3,
h4,
h5,
h6 {
  font-size: inherit;
  font-weight: inherit;
}

/*
Reset links to optimize for opt-in styling instead of opt-out.
*/

a {
  color: inherit;
  text-decoration: inherit;
}

/*
Add the correct font weight in Edge and Safari.
*/

b,
strong {
  font-weight: bolder;
}

/*
1. Use the user's configured `mono` font family by default.
2. Correct the odd `em` font sizing in all browsers.
*/

code,
kbd,
samp,
pre {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  /* 1 */
  font-size: 1em;
  /* 2 */
}

/*
Add the correct font size in all browsers.
*/

small {
  font-size: 80%;
}

/*
Prevent `sub` and `sup` elements from affecting the line height in all browsers.
*/

sub,
sup {
  font-size: 75%;
  line-height: 0;
  position: relative;
  vertical-align: baseline;
}

sub {
  bottom: -0.25em;
}

sup {
  top: -0.5em;
}

/*
1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
3. Remove gaps between table borders by default.
*/

table {
  text-indent: 0;
  /* 1 */
  border-color: inherit;
  /* 2 */
  border-collapse: collapse;
  /* 3 */
}

/*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/

button,
input,
optgroup,
select,
textarea {
  font-family: inherit;
  /* 1 */
  font-size: 100%;
  /* 1 */
  line-height: inherit;
  /* 1 */
  color: inherit;
  /* 1 */
  margin: 0;
  /* 2 */
  padding: 0;
  /* 3 */
}

/*
Remove the inheritance of text transform in Edge and Firefox.
*/

button,
select {
  text-transform: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/

button,
[type='button'],
[type='reset'],
[type='submit'] {
  -webkit-appearance: button;
  /* 1 */
  background-color: transparent;
  /* 2 */
  background-image: none;
  /* 2 */
}

/*
Use the modern Firefox focus style for all focusable elements.
*/

:-moz-focusring {
  outline: auto;
}

/*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/

:-moz-ui-invalid {
  box-shadow: none;
}

/*
Add the correct vertical alignment in Chrome and Firefox.
*/

progress {
  vertical-align: baseline;
}

/*
Correct the cursor style of increment and decrement buttons in Safari.
*/

::-webkit-inner-spin-button,
::-webkit-outer-spin-button {
  height: auto;
}

/*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/

[type='search'] {
  -webkit-appearance: textfield;
  /* 1 */
  outline-offset: -2px;
  /* 2 */
}

/*
Remove the inner padding in Chrome and Safari on macOS.
*/

::-webkit-search-decoration {
  -webkit-appearance: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/

::-webkit-file-upload-button {
  -webkit-appearance: button;
  /* 1 */
  font: inherit;
  /* 2 */
}

/*
Add the correct display in Chrome and Safari.
*/

summary {
  display: list-item;
}

/*
Removes the default spacing and border for appropriate elements.
*/

blockquote,
dl,
dd,
h1,
h2,
h3,
h4,
h5,
h6,
hr,
figure,
p,
pre {
  margin: 0;
}

fieldset {
  margin: 0;
  padding: 0;
}

legend {
  padding: 0;
}

ol,
ul,
menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

/*
Prevent resizing textareas horizontally by default.
*/

textarea {
  resize: vertical;
}

/*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/

input::-moz-placeholder, textarea::-moz-placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

input:-ms-input-placeholder, textarea:-ms-input-placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

input::placeholder,
textarea::placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

/*
Set the default cursor for buttons.
*/

button,
[role="button"] {
  cursor: pointer;
}

/*
Make sure disabled buttons don't get the pointer cursor.
*/

:disabled {
  cursor: default;
}

/*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
   This can trigger a poorly considered lint error in some tools but is included by design.
*/

img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display: block;
  /* 1 */
  vertical-align: middle;
  /* 2 */
}

/*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/

img,
video {
  max-width: 100%;
  height: auto;
}

/*
Ensure the default browser behavior of the `hidden` attribute.
*/

[hidden] {
  display: none;
}

*, ::before, ::after{
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x:  ;
  --tw-pan-y:  ;
  --tw-pinch-zoom:  ;
  --tw-scroll-snap-strictness: proximity;
  --tw-ordinal:  ;
  --tw-slashed-zero:  ;
  --tw-numeric-figure:  ;
  --tw-numeric-spacing:  ;
  --tw-numeric-fraction:  ;
  --tw-ring-inset:  ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / 0.5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur:  ;
  --tw-brightness:  ;
  --tw-contrast:  ;
  --tw-grayscale:  ;
  --tw-hue-rotate:  ;
  --tw-invert:  ;
  --tw-saturate:  ;
  --tw-sepia:  ;
  --tw-drop-shadow:  ;
  --tw-backdrop-blur:  ;
  --tw-backdrop-brightness:  ;
  --tw-backdrop-contrast:  ;
  --tw-backdrop-grayscale:  ;
  --tw-backdrop-hue-rotate:  ;
  --tw-backdrop-invert:  ;
  --tw-backdrop-opacity:  ;
  --tw-backdrop-saturate:  ;
  --tw-backdrop-sepia:  ;
}

.sr-only{
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

.absolute{
  position: absolute;
}

.relative{
  position: relative;
}

.z-0{
  z-index: 0;
}

.z-10{
  z-index: 10;
}

.m-0{
  margin: 0px;
}

.mx-\[auto\]{
  margin-left: auto;
  margin-right: auto;
}

.mx-\[22px\]{
  margin-left: 22px;
  margin-right: 22px;
}

.mx-\[40px\]{
  margin-left: 40px;
  margin-right: 40px;
}

.mb-\[30px\]{
  margin-bottom: 30px;
}

.mb-\[11px\]{
  margin-bottom: 11px;
}

.mt-\[30px\]{
  margin-top: 30px;
}

.mb-\[21px\]{
  margin-bottom: 21px;
}

.mb-4{
  margin-bottom: 1rem;
}

.mb-\[10px\]{
  margin-bottom: 10px;
}

.mt-5{
  margin-top: 1.25rem;
}

.mt-\[50px\]{
  margin-top: 50px;
}

.mb-\[50px\]{
  margin-bottom: 50px;
}

.mb-\[8px\]{
  margin-bottom: 8px;
}

.ml-\[5px\]{
  margin-left: 5px;
}

.mb-\[24px\]{
  margin-bottom: 24px;
}

.mt-\[auto\]{
  margin-top: auto;
}

.mb-\[26px\]{
  margin-bottom: 26px;
}

.mb-\[25px\]{
  margin-bottom: 25px;
}

.mb-\[20px\]{
  margin-bottom: 20px;
}

.mb-\[5px\]{
  margin-bottom: 5px;
}

.mb-\[40px\]{
  margin-bottom: 40px;
}

.mr-5{
  margin-right: 1.25rem;
}

.ml-5{
  margin-left: 1.25rem;
}

.ml-\[10px\]{
  margin-left: 10px;
}

.mb-6{
  margin-bottom: 1.5rem;
}

.mb-8{
  margin-bottom: 2rem;
}

.mb-1\.5{
  margin-bottom: 0.375rem;
}

.mb-1{
  margin-bottom: 0.25rem;
}

.mb-5{
  margin-bottom: 1.25rem;
}

.mb-\[36px\]{
  margin-bottom: 36px;
}

.block{
  display: block;
}

.flex{
  display: flex;
}

.inline-flex{
  display: inline-flex;
}

.hidden{
  display: none;
}

.h-\[40px\]{
  height: 40px;
}

.min-h-\[450px\]{
  min-height: 450px;
}

.min-h-\[57\%\]{
  min-height: 57%;
}

.min-h-\[411px\]{
  min-height: 411px;
}

.w-\[100\%\]{
  width: 100%;
}

.w-full{
  width: 100%;
}

.w-\[87px\]{
  width: 87px;
}

.max-w-\[625px\]{
  max-width: 625px;
}

.max-w-\[212px\]{
  max-width: 212px;
}

.max-w-\[450px\]{
  max-width: 450px;
}

.flex-1{
  flex: 1 1 0%;
}

.grow{
  flex-grow: 1;
}

.transform{
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.cursor-pointer{
  cursor: pointer;
}

.flex-col{
  flex-direction: column;
}

.flex-col-reverse{
  flex-direction: column-reverse;
}

.content-start{
  align-content: flex-start;
}

.items-start{
  align-items: flex-start;
}

.items-center{
  align-items: center;
}

.justify-start{
  justify-content: flex-start;
}

.justify-end{
  justify-content: flex-end;
}

.justify-center{
  justify-content: center;
}

.justify-between{
  justify-content: space-between;
}

.gap-\[25px\]{
  gap: 25px;
}

.gap-\[13px\]{
  gap: 13px;
}

.gap-\[10px\]{
  gap: 10px;
}

.gap-\[44px\]{
  gap: 44px;
}

.gap-x-2{
  -moz-column-gap: 0.5rem;
       column-gap: 0.5rem;
}

.gap-y-\[20px\]{
  row-gap: 20px;
}

.overflow-hidden{
  overflow: hidden;
}

.overflow-y-auto{
  overflow-y: auto;
}

.rounded{
  border-radius: 0.25rem;
}

.rounded-sm{
  border-radius: 5px;
}

.rounded-full{
  border-radius: 9999px;
}

.rounded-lg{
  border-radius: 0.5rem;
}

.rounded-\[10px\]{
  border-radius: 10px;
}

.border{
  border-width: 1px;
}

.border-\[10px\]{
  border-width: 10px;
}

.border-t-\[0px\]{
  border-top-width: 0px;
}

.border-t-\[10px\]{
  border-top-width: 10px;
}

.border-t-\[1px\]{
  border-top-width: 1px;
}

.border-t-\[11px\]{
  border-top-width: 11px;
}

.border-light-grey{
  --tw-border-opacity: 1;
  border-color: rgb(232 233 237 / var(--tw-border-opacity));
}

.border-primary-blue{
  --tw-border-opacity: 1;
  border-color: rgb(0 145 234 / var(--tw-border-opacity));
}

.border-\[transparent\]{
  border-color: transparent;
}

.border-\[\#28CA42\]{
  --tw-border-opacity: 1;
  border-color: rgb(40 202 66 / var(--tw-border-opacity));
}

.border-primary-green{
  --tw-border-opacity: 1;
  border-color: rgb(40 202 66 / var(--tw-border-opacity));
}

.bg-secondary-dark{
  --tw-bg-opacity: 1;
  background-color: rgb(73 68 68 / var(--tw-bg-opacity));
}

.bg-white{
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity));
}

.bg-primary-blue{
  --tw-bg-opacity: 1;
  background-color: rgb(0 145 234 / var(--tw-bg-opacity));
}

.bg-primary-blue\/\[\.05\]{
  background-color: rgb(0 145 234 / .05);
}

.bg-primary-grey{
  --tw-bg-opacity: 1;
  background-color: rgb(229 229 229 / var(--tw-bg-opacity));
}

.bg-primary-blue\/50{
  background-color: rgb(0 145 234 / 0.5);
}

.bg-primary-blue\/5{
  background-color: rgb(0 145 234 / 0.05);
}

.bg-\[\#F2F9FE\]{
  --tw-bg-opacity: 1;
  background-color: rgb(242 249 254 / var(--tw-bg-opacity));
}

.bg-clip-padding{
  background-clip: padding-box;
}

.p-4{
  padding: 1rem;
}

.p-\[20px\]{
  padding: 20px;
}

.p-\[12px\]{
  padding: 12px;
}

.py-\[36px\]{
  padding-top: 36px;
  padding-bottom: 36px;
}

.px-\[20px\]{
  padding-left: 20px;
  padding-right: 20px;
}

.py-\[33px\]{
  padding-top: 33px;
  padding-bottom: 33px;
}

.px-3{
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}

.py-2{
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.py-\[9\.5px\]{
  padding-top: 9.5px;
  padding-bottom: 9.5px;
}

.px-\[12px\]{
  padding-left: 12px;
  padding-right: 12px;
}

.py-\[22px\]{
  padding-top: 22px;
  padding-bottom: 22px;
}

.px-\[22px\]{
  padding-left: 22px;
  padding-right: 22px;
}

.pt-\[20px\]{
  padding-top: 20px;
}

.pl-\[18px\]{
  padding-left: 18px;
}

.pr-\[18px\]{
  padding-right: 18px;
}

.pb-\[20px\]{
  padding-bottom: 20px;
}

.pt-\[17px\]{
  padding-top: 17px;
}

.pb-\[23px\]{
  padding-bottom: 23px;
}

.pt-\[21px\]{
  padding-top: 21px;
}

.pb-\[26px\]{
  padding-bottom: 26px;
}

.pb-\[58px\]{
  padding-bottom: 58px;
}

.pt-\[35px\]{
  padding-top: 35px;
}

.pb-\[34px\]{
  padding-bottom: 34px;
}

.pt-\[29px\]{
  padding-top: 29px;
}

.pb-\[56px\]{
  padding-bottom: 56px;
}

.text-left{
  text-align: left;
}

.text-center{
  text-align: center;
}

.font-\[\'Open_Sans\'\]{
  font-family: 'Open Sans';
}

.text-\[15px\]{
  font-size: 15px;
}

.text-\[24px\]{
  font-size: 24px;
}

.text-\[18px\]{
  font-size: 18px;
}

.text-base{
  font-size: 1rem;
  line-height: 1.5rem;
}

.text-sm{
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.text-\[12px\]{
  font-size: 12px;
}

.text-\[40px\]{
  font-size: 40px;
}

.text-\[14px\]{
  font-size: 14px;
}

.text-\[16px\]{
  font-size: 16px;
}

.text-\[22px\]{
  font-size: 22px;
}

.text-\[35px\]{
  font-size: 35px;
}

.font-normal{
  font-weight: 400;
}

.font-semibold{
  font-weight: 600;
}

.font-bold{
  font-weight: 700;
}

.uppercase{
  text-transform: uppercase;
}

.italic{
  font-style: italic;
}

.leading-\[1\.75em\]{
  line-height: 1.75em;
}

.leading-\[1\.3em\]{
  line-height: 1.3em;
}

.leading-\[1\.35em\]{
  line-height: 1.35em;
}

.leading-\[1\.36em\]{
  line-height: 1.36em;
}

.leading-\[1\.5em\]{
  line-height: 1.5em;
}

.leading-\[1\.375em\]{
  line-height: 1.375em;
}

.leading-\[1\.2rem\]{
  line-height: 1.2rem;
}

.text-white{
  --tw-text-opacity: 1;
  color: rgb(255 255 255 / var(--tw-text-opacity));
}

.text-primary-blue{
  --tw-text-opacity: 1;
  color: rgb(0 145 234 / var(--tw-text-opacity));
}

.text-dark-grey{
  --tw-text-opacity: 1;
  color: rgb(142 147 167 / var(--tw-text-opacity));
}

.text-light-grey-secondary{
  --tw-text-opacity: 1;
  color: rgb(219 220 222 / var(--tw-text-opacity));
}

.text-black{
  --tw-text-opacity: 1;
  color: rgb(0 0 0 / var(--tw-text-opacity));
}

.text-primary-dark{
  --tw-text-opacity: 1;
  color: rgb(46 44 44 / var(--tw-text-opacity));
}

.text-gray{
  --tw-text-opacity: 1;
  color: rgb(100 111 121 / var(--tw-text-opacity));
}

.transition{
  transition-property: color, background-color, border-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-text-decoration-color, -webkit-backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-text-decoration-color, -webkit-backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.duration-300{
  transition-duration: 300ms;
}

.ease-in-out{
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.ease-in{
  transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
}

/* UI Elements */

.button-primary {
  width: 100%;
  height: 49px;
  color: #fff;
  cursor: pointer;
}

.list-checkmark-round {
  padding: 0;
  list-style: none;
}

.list-checkmark-round li {
  position: relative;
  padding-left: 40px;
}

.list-checkmark-round li::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0px;
  width: 30px;
  height: 30px;
  background: url('/images/checkmark-round.svg') center/contain no-repeat;
}

.list-checkmark {
  padding: 0;
  list-style: none;
}

.list-checkmark li {
  position: relative;
  padding-left: 25px;
}

.list-checkmark li::before {
  content: "";
  position: absolute;
  left: 0;
  top: 4px;
  width: 16px;
  height: 16px;
  background: url('/images/checkmark.svg') center/contain no-repeat;
}

.list-checkmark_green li::before {
  background: url('/images/checkmark-green.svg') center/contain no-repeat;
}

/* Main Content */

.pro-plan-trial {
  position: relative;
  overflow: hidden;
}

.success-banner {
  position: relative;
  overflow: hidden;
}

.success-banner::before{
  content: "";
  position: absolute;
  left: 0%;
  bottom: 0;
  width: 60%;
  height: 100%;
  background: url(/images/test.svg) right center/cover no-repeat;
  z-index: 1;
}

@media (max-width: 767px) {
  .success-banner::before {
    width: 100%;
  }
}

.after\:absolute::after{
  content: var(--tw-content);
  position: absolute;
}

.after\:top-\[5px\]::after{
  content: var(--tw-content);
  top: 5px;
}

.after\:left-\[8px\]::after{
  content: var(--tw-content);
  left: 8px;
}

.after\:h-\[30px\]::after{
  content: var(--tw-content);
  height: 30px;
}

.after\:w-\[30px\]::after{
  content: var(--tw-content);
  width: 30px;
}

.after\:rounded-full::after{
  content: var(--tw-content);
  border-radius: 9999px;
}

.after\:bg-white::after{
  content: var(--tw-content);
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity));
}

.after\:transition-all::after{
  content: var(--tw-content);
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.after\:content-\[\'\'\]::after{
  --tw-content: '';
  content: var(--tw-content);
}

.hover\:bg-primary-green:hover{
  --tw-bg-opacity: 1;
  background-color: rgb(40 202 66 / var(--tw-bg-opacity));
}

.hover\:text-white:hover{
  --tw-text-opacity: 1;
  color: rgb(255 255 255 / var(--tw-text-opacity));
}

.hover\:opacity-80:hover{
  opacity: 0.8;
}

.focus\:border-primary-blue:focus{
  --tw-border-opacity: 1;
  border-color: rgb(0 145 234 / var(--tw-border-opacity));
}

.focus\:outline-none:focus{
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.peer:checked ~ .peer-checked\:bg-primary-blue{
  --tw-bg-opacity: 1;
  background-color: rgb(0 145 234 / var(--tw-bg-opacity));
}

.peer:checked ~ .peer-checked\:text-gray{
  --tw-text-opacity: 1;
  color: rgb(100 111 121 / var(--tw-text-opacity));
}

.peer:checked ~ .peer-checked\:text-black{
  --tw-text-opacity: 1;
  color: rgb(0 0 0 / var(--tw-text-opacity));
}

.peer:checked ~ .peer-checked\:after\:translate-x-\[140\%\]::after{
  content: var(--tw-content);
  --tw-translate-x: 140%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.peer:focus ~ .peer-focus\:outline-none{
  outline: 2px solid transparent;
  outline-offset: 2px;
}

@media (min-width: 768px){
  .md\:mx-\[0\]{
    margin-left: 0;
    margin-right: 0;
  }

  .md\:mb-\[46px\]{
    margin-bottom: 46px;
  }

  .md\:flex{
    display: flex;
  }

  .md\:min-h-\[411px\]{
    min-height: 411px;
  }

  .md\:w-1\/2{
    width: 50%;
  }

  .md\:w-1\/3{
    width: 33.333333%;
  }

  .md\:shrink{
    flex-shrink: 1;
  }

  .md\:grow-0{
    flex-grow: 0;
  }

  .md\:basis-\[449px\]{
    flex-basis: 449px;
  }

  .md\:flex-row{
    flex-direction: row;
  }

  .md\:gap-\[18px\]{
    gap: 18px;
  }

  .md\:gap-x-\[21px\]{
    -moz-column-gap: 21px;
         column-gap: 21px;
  }

  .md\:px-\[58px\]{
    padding-left: 58px;
    padding-right: 58px;
  }

  .md\:px-\[40px\]{
    padding-left: 40px;
    padding-right: 40px;
  }

  @media (min-width: 768px){
    .md\:md\:px-\[40px\]{
      padding-left: 40px;
      padding-right: 40px;
    }
  }

  .md\:pl-\[61px\]{
    padding-left: 61px;
  }

  .md\:pr-\[20px\]{
    padding-right: 20px;
  }

  .md\:pt-\[58px\]{
    padding-top: 58px;
  }

  .md\:pb-\[40px\]{
    padding-bottom: 40px;
  }

  .md\:pl-\[52px\]{
    padding-left: 52px;
  }

  .md\:pr-\[48px\]{
    padding-right: 48px;
  }

  .md\:text-left{
    text-align: left;
  }

  .md\:text-\[30px\]{
    font-size: 30px;
  }
}

@media (min-width: 1024px){
  .lg\:max-w-\[80\%\]{
    max-width: 80%;
  }
}
</style>
@endpush

<main
  class="font-['Open_Sans'] text-[15px] font-normal leading-[1.75em] flex-1 relative z-0 overflow-y-auto pt-[20px] pl-[18px] pr-[18px] pb-[20px] focus:outline-none"
  tabindex="0"
>
  <div class="min-h-[450px] flex justify-stretch overflow-hidden">
    <div class="success-banner p-[20px] w-[100%] gap-[44px] flex flex-col-reverse justify-center align-stretch bg-secondary-dark text-white rounded overflow-hidden md:flex-row">
      <div class="success-banner__media flex flex-col justify-senter align-center">
        <img class="w-[100%] max-w-[625px] z-10" src="./images/hero-success.svg" alt="">
      </div>
      <div class="flex flex-col justify-center align-center text-center z-10 md:text-left">
      <h4 class="text-[14px] mb-[10px] text-light-grey-secondary uppercase">Create. Send. Get Paid.</h4>
        <h2 class="text-[24px] font-bold leading-[1.5em] mb-[26px] relative md:text-[30px] md:mb-[46px]">
          Your Pro Plan trial is <br/>loaded and ready to go!
        </h2>
        <a href="https://app.invoicing.co/#/"
              type="button"
              class="mx-[auto] max-w-[212px] bg-primary-blue hover:opacity-80 button button-primary bg-primary rounded-sm text-sm transition duration-300 ease-in md:mx-[0]"
            >
            Account Login
            </a>
      </div>
    </div>
  </div>
</main>
@endsection