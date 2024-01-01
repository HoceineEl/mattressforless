<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery/>
</v-product-gallery>

@pushOnce('scripts')
    <script type="text/x-template" id="v-product-gallery-template">
        <div class="flex flex-col gap-[30px] h-full sticky top-[30px] max-1180:hidden">
        
            
            <!-- Media shimmer Effect -->
            <div
                class="max-w-[1000px] max-h-[900px]"
                v-show="isMediaLoading"
            >
                <div class="min-w-[560px] min-h-[607px] bg-[#E9E9E9] rounded-[12px] shimmer"></div>
            </div>

            <div
                class="w-[1000px] max-h-[900px]"
                v-show="! isMediaLoading"
            >
                <img 
                    class="w-full h-full object-cover rounded-[12px]" 
                    :src="baseFile.path" 
                    v-if="baseFile.type == 'image'"
                    alt="@lang('shop::app.products.view.gallery.product-image')"
                    @load="onMediaLoad()"
                />

                <div
                    class="min-w-full rounded-[12px]"
                    v-if="baseFile.type == 'video'"
                >
                    <video  
                        controls    
                        class="w-full h-full"
                        @load="onMediaLoad()"
                    >
                        <source 
                            :src="baseFile.path" 
                            type="video/mp4"
                        />
                    </video>    
                </div>
                
            </div>
                <!-- Product Image Slider -->
            <div class=" flex  place-content-start h-509 overflow-x-hidden overflow-y-auto  gap-[30px]">
                <img 
                    :class="`min-w-[100px] max-h-[100px] rounded-[12px] ${ hover ? 'cursor-pointer' : '' }`" 
                    v-for="image in media.images"
                    :src="image.small_image_url"
                    alt="@lang('shop::app.products.view.gallery.thumbnail-image')"
                    width="100"
                    height="100"
                    @mouseover="change(image)"
                />

                <!-- Need to Set Play Button  -->
                <video 
                    class="min-w-[100px] rounded-[12px]"
                    v-for="video in media.videos"
                    @mouseover="change(video)"
                >
                    <source 
                        :src="video.video_url" 
                        type="video/mp4"
                    />
                </video>
            </div>
        </div>

        <!-- Product slider Image with shimmer -->
        <div class="flex gap-[30px] 1180:hidden overflow-auto scrollbar-hide">
            <x-shop::media.images.lazy
                ::src="image.large_image_url"
                class="min-w-[450px] max-sm:min-w-full w-[490px]" 
                v-for="image in media.images"
            >
            </x-shop::media.images.lazy>
        </div>
    </script>

    <script type="module">
        app.component('v-product-gallery', {
            template: '#v-product-gallery-template',

            data() {
                return {
                    isMediaLoading: true,

                    media: {
                        images: @json(product_image()->getGalleryImages($product)),

                        videos: @json(product_video()->getVideos($product)),
                    },

                    baseFile: {
                        type: '',

                        path: ''
                    },

                    hover: false,
                }
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages)) {
                            this.baseFile.path = newImages[0].large_image_url; 
                        }
                    },
                },
            },

            mounted() {
                if (this.media.images.length) {
                    this.baseFile.type = 'image';
                    this.baseFile.path = this.media.images[0].large_image_url;
                } else {
                    this.baseFile.type = 'video';
                    this.baseFile.path = this.media.videos[0].video_url;
                }
            },

            methods: {
                onMediaLoad() {
                    this.isMediaLoading = false;
                },

                change(file) {
                    if (file.type == 'videos') {
                        this.baseFile.type = 'video';

                        this.baseFile.path = file.video_url;
                    } else {
                        this.baseFile.type = 'image';

                        this.baseFile.path = file.large_image_url;
                    }
                    
                    this.hover = true;
                }
            }
        })
    </script>
@endpushOnce
