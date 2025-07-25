<div class="w-full max-w-4xl mx-auto px-8 py-10 bg-white/30 backdrop-blur-md border border-white/40 shadow-xl rounded-xl md:min-h-[22rem]">
    <div class="flex flex-col md:flex-row items-center md:items-stretch gap-0 h-full">
        <!-- Logo Column -->
        <div class="flex flex-col items-center justify-center md:w-1/2 w-full h-full py-4">
            <img src="/images/publication_logo.png" alt="Publication Logo" class="h-full w-full object-contain" />
        </div>
        <!-- Form/Content Column -->
        <div class="flex-1 flex flex-col justify-center md:pl-8 w-full">
        {{ $slot }}
        </div>
    </div>
</div>
