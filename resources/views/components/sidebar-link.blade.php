@props(['href', 'active', 'icon', 'label'])

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'group flex items-center px-3 py-3 rounded-xl transition-all duration-200 ' . 
   ($active 
    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' 
    : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200')]) }}>
    
    <div class="flex items-center flex-1">
        <i class="{{ $icon }} w-6 text-center text-lg {{ $active ? 'text-white' : 'group-hover:text-indigo-400' }}"></i>
        
        <span x-show="sidebarOpen" 
              x-transition:enter="transition ease-out duration-200"
              class="ml-3 font-bold text-sm tracking-wide whitespace-nowrap">
            {{ $label }}
        </span>
    </div>

    @if($active)
        <div x-show="sidebarOpen" class="w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_8px_rgba(255,255,255,0.8)]"></div>
    @endif
</a>