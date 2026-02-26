
<div class="audit-log" role="table" aria-label="Login history">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        IP Address
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Time
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($loginAttempts as $attempt)
                <tr :class="rowClasses($attempt->suspicious)">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $attempt->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $attempt->ip_address }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $attempt->location }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="status-badge" :class="statusColor($attempt->status)">
                            {{ $attempt->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $attempt->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>