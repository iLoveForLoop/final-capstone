export function isPricePackage(service) {
    if (service.catering_service) {
        if (
            service.catering_service.price !==
            service.catering_service.package_price
        ) {
            return 'per pax';
        } else {
            return null;
        }
    }
    return null;
}
