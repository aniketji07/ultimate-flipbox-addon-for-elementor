
class UfaeCommon extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                flipboxContainer: '.ufae-container',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $flipboxContainer: this.$element.find(selectors.flipboxContainer),
        };
    }

    bindEvents() {
        const flipboxContainer = this.elements.$flipboxContainer;
        const editorActiveWidget = flipboxContainer.closest('.elementor-widget-ufae_flipbox_widget.elementor-element-editable')[0];
        if (editorActiveWidget && editorActiveWidget.dataset['ufaeActiveRepeater']) {
            const activeRepeaterId = editorActiveWidget.dataset.ufaeActiveRepeater;
            flipboxContainer.find(`.ufae-flipbox-item.elementor-repeater-item-${activeRepeaterId}`).addClass('ufae-repeater-active');
        }
    }

}


jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(UfaeCommon, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ufae_flipbox_widget.default', addHandler);

});