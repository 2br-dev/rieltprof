class OffersInTable {
    constructor() {
        this.selector = {
            offerToggle: '.offer-block-toggle',
            row: 'tr',
        };
        this.class = {
            offersLoaded: 'offers-loaded',
            offersOpen: 'offers-open',
            productOffer: 'product-offer',
            hidden: 'rs-hidden',
        };

        document.addEventListener('click', (event) => {
            let target = event.target;
            let toggle = target.closest(this.selector.offerToggle);
            if (toggle) {
                let row = toggle.closest(this.selector.row);
                this.toggleOffers(row);
            }
        });
    }

    toggleOffers(element) {
        if (!element.classList.contains(this.class.offersLoaded)) {
            this.loadOffers(element);
        }
        element.classList.toggle(this.class.offersOpen);
        this.toggleOffersElements(element, element.classList.contains(this.class.offersOpen));
    }

    loadOffers(element) {
        let url = element.querySelector(this.selector.offerToggle).dataset.urlLoadOffers;
        element.classList.add(this.class.offersLoaded);
        $.ajaxQuery({
            url: url,
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    element.insertAdjacentHTML('afterend', response.html);
                    this.toggleOffersElements(element, element.classList.contains(this.class.offersOpen));
                    $(document).trigger('new-content');
                }
            }
        });
    }

    toggleOffersElements(element, open) {
        while (element) {
            element = element.nextElementSibling;
            if (element && element.classList.contains(this.class.productOffer)) {
                if (open) {
                    element.classList.remove(this.class.hidden);
                } else {
                    element.classList.add(this.class.hidden);
                }
            } else {
                break;
            }
        }
    }
}

document.offersInTable = new OffersInTable();
