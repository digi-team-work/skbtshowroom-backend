'use strict';
import { createControlComponentConfig, KoStandaloneControl } from '../control-base.js';
/**
 * Image selector control.
 *
 * This implementation hands off the work to the existing AmeImageSelectorApi.ImageSelector
 * class to avoid duplicating the effort. That class is not a module because it is also
 * used for the more progressive-enhancement-y PHP-rendered controls, so we can't import it.
 */
class AmeImageSelector extends KoStandaloneControl {
    constructor(params, $element) {
        super(params, $element);
        this.selectorInstance = null;
        //Verify that our dependencies are available.
        if (typeof AmeImageSelectorApi === 'undefined') {
            throw new Error('AmeImageSelectorApi is not available. Remember to enqueue "ame-image-selector-control-v2".');
        }
        if (typeof AmeImageSelectorApi.ImageSelector === 'undefined') {
            throw new Error('AmeImageSelectorApi.ImageSelector is not available. This is probably a bug.');
        }
        this.externalUrlsAllowed = !!params.externalUrlsAllowed;
        this.canSelectMedia = !!params.canSelectMedia;
        this.imageProxy = this.settings.value.value;
    }
    get classes() {
        return [
            'ame-image-selector-v2',
            ...super.classes,
        ];
    }
    koDescendantsComplete() {
        const $container = this.findChild('.ame-image-selector-v2');
        if ($container.length === 0) {
            return;
        }
        this.selectorInstance = new AmeImageSelectorApi.ImageSelector($container, {
            externalUrlsAllowed: this.externalUrlsAllowed,
            canSelectMedia: this.canSelectMedia,
        }, this.imageProxy());
    }
}
export default createControlComponentConfig(AmeImageSelector, `
	<div class="ame-image-selector-v2" data-ame-is-component="1" 
		data-bind="class: classString, ameObservableChangeEvents: { observable: imageProxy }">
		<!-- The contents should be generated by the image selector API. -->
	</div>
`);
//# sourceMappingURL=ame-image-selector.js.map