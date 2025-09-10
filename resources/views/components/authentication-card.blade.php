<div class="w-full max-w-4xl mx-auto bg-white/30 backdrop-blur-md border border-white/40 shadow-xl rounded-xl min-h-[32rem] md:min-h-[36rem]">
    <div class="flex flex-col md:flex-row items-center md:items-stretch gap-0 h-full min-h-[32rem] md:min-h-[36rem]">
        <!-- Logo Column -->
        <div class="hidden md:flex flex-col items-center justify-center md:w-1/2 w-full min-h-[32rem] md:min-h-[36rem] p-4 pl-8 md:pr-3">
            <img src="/images/publication_logo.webp" alt="Publication Logo" class="max-h-full max-w-full object-contain" />
        </div>
        <!-- Form/Content Column -->
        <div class="flex-1 flex flex-col justify-center w-full px-8 md:pl-3 py-10">
            {{ $slot }}
        </div>
    </div>
</div>
