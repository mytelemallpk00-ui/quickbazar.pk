<script>
    const currentProduct = @json($currentProductCatalog);
    const relatedProducts = @json($relatedProductsCatalog);
    const galleryImages = @json($galleryImages);
    window.currentProduct = currentProduct;
    window.relatedProducts = relatedProducts;
    window.products = [currentProduct].concat(relatedProducts || []);
    window.galleryImages = galleryImages;
</script>
