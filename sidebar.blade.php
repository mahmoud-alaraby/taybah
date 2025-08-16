<div x-data="{ open: false }" class="relative w-full">
  <button @click="open = !open"
          class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white transition-colors focus:outline-none"
          aria-haspopup="true" aria-expanded="open" type="button">
    <i class="fas fa-comments ml-3 text-sm"></i>
    متابعة العمل
    <svg :class="{'rotate-180': open}" class="w-4 h-4 ml-1 mt-1 text-gray-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg>
  </button>

  <div x-show="open" @click.away="open = false"
       x-transition:enter="transition ease-out duration-200"
       x-transition:leave="transition ease-in duration-150"
       class="absolute left-0 top-full mt-1 w-full bg-gray-800 rounded-md shadow-lg z-20 origin-top-left">
    
    <a href="{{ route('admin.work-chat.index', ['type' => 'design']) }}"
       class="flex items-center w-full px-3 py-2 my-2 text-sm font-medium rounded-md text-gray-300 hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('admin.work-chat.*') && request('type') === 'design' ? 'bg-red-600 text-white' : '' }}">
      <i class="fas fa-pencil-ruler ml-3 text-sm"></i>
      متابعة التصميم
    </a>
    
    <a href="{{ route('admin.work-chat.index', ['type' => 'montage']) }}"
       class="flex items-center w-full px-3 py-2 my-2 text-sm font-medium rounded-md text-gray-300 hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('admin.work-chat.*') && request('type') === 'montage' ? 'bg-red-600 text-white' : '' }}">
      <i class="fas fa-video ml-3 text-sm"></i>
      متابعة المونتاج
    </a>
  </div>
</div>







          {{--   الي في الموظف  يجب حذفهم--}}
      // احذف هذين الجزأين من الكود:

@if(auth('employee')->user()->hasPermission('design_follow_up'))
<a href="{{ route('employee.design-follow-up') }}"...>
  متابعة التصميم
</a>
@endif

@if(auth('employee')->user()->hasPermission('montage_follow_up'))
<a href="{{ route('employee.montage-follow-up') }}"...>
  متابعة المونتاج  
</a>
@endif