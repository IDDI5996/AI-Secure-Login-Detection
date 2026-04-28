<div>
    @if($activities->isEmpty())
        <div class="text-center py-8 text-gray-500">
            <p>No recent activity yet.</p>
        </div>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach($activities as $attempt)
                <li class="py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $attempt->user->name ?? 'Unknown User' }} 
                            <span class="text-xs text-gray-500">({{ $attempt->email }})</span>
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $attempt->ip_address }} – {{ $attempt->country ?? 'Unknown' }} – 
                            <span class="{{ $attempt->is_successful ? 'text-green-600' : 'text-red-600' }}">
                                {{ $attempt->is_successful ? 'Success' : 'Failed' }}
                            </span>
                            @if($attempt->is_suspicious)
                                <span class="ml-1 bg-red-100 text-red-800 px-1.5 py-0.5 rounded text-xs">Suspicious</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">{{ $attempt->attempted_at->diffForHumans() }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>