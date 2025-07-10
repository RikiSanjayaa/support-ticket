<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Reply;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        // Create support agents
        $agent1 = User::factory()->create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@company.com',
            'password' => 'password',
            'role' => 'agent',
        ]);

        $agent2 = User::factory()->create([
            'name' => 'Mike Chen',
            'email' => 'mike@company.com',
            'password' => 'password',
            'role' => 'agent',
        ]);

        // Create regular users
        $user1 = User::factory()->create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Emily Davis',
            'email' => 'emily@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        $user3 = User::factory()->create([
            'name' => 'Robert Wilson',
            'email' => 'robert@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        // Create additional random users
        User::factory(5)->create(['role' => 'user']);
        User::factory(2)->create(['role' => 'agent']);

        // Create realistic support tickets with replies
        $this->createRealisticTickets($user1, $user2, $user3, $agent1, $agent2);
    }

    private function createRealisticTickets($user1, $user2, $user3, $agent1, $agent2)
    {
        // Ticket 1: Password Reset Issue (Resolved)
        $ticket1 = Ticket::create([
            'title' => 'Unable to reset my password',
            'description' => 'Hi, I\'ve been trying to reset my password for the past hour but I\'m not receiving the reset email. I\'ve checked my spam folder and tried multiple times. My username is john.smith123. Please help!',
            'status' => 'closed',
            'created_by' => $user1->id,
            'assigned_to' => $agent1->id,
            'resolved_by' => $agent1->id,
            'resolved_at' => now()->subDays(2),
            'created_at' => now()->subDays(3),
        ]);

        Reply::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $agent1->id,
            'content' => 'Hi John, thanks for reaching out. I can see the issue - there was a temporary problem with our email service. I\'ve manually reset your password and sent you a new temporary password to your email. Please check your inbox and let me know if you receive it.',
            'created_at' => now()->subDays(3)->addHours(2),
        ]);

        Reply::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $user1->id,
            'content' => 'Perfect! I received the email and was able to log in with the temporary password. I\'ve already changed it to something more secure. Thank you so much for the quick help!',
            'created_at' => now()->subDays(3)->addHours(3),
        ]);

        Reply::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $agent1->id,
            'content' => 'Excellent! I\'m glad we got that sorted out for you. If you have any other issues in the future, don\'t hesitate to reach out. Marking this ticket as resolved.',
            'created_at' => now()->subDays(2),
        ]);

        // Ticket 2: Billing Question (Open)
        $ticket2 = Ticket::create([
            'title' => 'Question about my invoice #INV-2024-1234',
            'description' => 'Hello, I received my monthly invoice but I notice there\'s a charge for "Premium Features" that I don\'t remember subscribing to. The amount is $29.99. Could you please explain what this charge is for? I only signed up for the basic plan. My account email is emily@example.com.',
            'status' => 'open',
            'created_by' => $user2->id,
            'assigned_to' => $agent2->id,
            'created_at' => now()->subDays(1),
        ]);

        Reply::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $agent2->id,
            'content' => 'Hi Emily, thank you for bringing this to our attention. I\'ve reviewed your account and I can see that on January 15th, there was an upgrade to Premium Features activated. This might have been done accidentally. Let me investigate this further and I\'ll get back to you within 24 hours with a detailed explanation and resolution.',
            'created_at' => now()->subHours(6),
        ]);

        // Ticket 3: Technical Issue (In Progress)
        $ticket3 = Ticket::create([
            'title' => 'Dashboard not loading - getting error 500',
            'description' => 'Since this morning, I can\'t access my dashboard. Every time I try to log in and go to the main dashboard, I get an "Internal Server Error 500" message. I\'ve tried different browsers (Chrome, Firefox, Safari) and cleared my cache, but the problem persists. This is urgent as I need to access my data for a client presentation tomorrow.',
            'status' => 'in_progress',
            'created_by' => $user3->id,
            'assigned_to' => $agent1->id,
            'created_at' => now()->subHours(8),
        ]);

        Reply::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $agent1->id,
            'content' => 'Hi Robert, I understand how urgent this is for your presentation. I\'ve escalated this to our technical team and we\'ve identified that there\'s an issue with the dashboard module that\'s affecting a small number of users. Our developers are working on a fix right now.',
            'created_at' => now()->subHours(6),
        ]);

        Reply::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $user3->id,
            'content' => 'Thank you for the quick response. Do you have any estimate on when this might be fixed? My presentation is at 2 PM tomorrow. Is there any workaround I can use in the meantime to access my data?',
            'created_at' => now()->subHours(4),
        ]);

        Reply::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $agent1->id,
            'content' => 'I completely understand your concern. As a workaround, I can export your dashboard data as a CSV file and email it to you within the next hour. This should give you access to all your data for the presentation. Meanwhile, we expect the dashboard fix to be deployed by tonight.',
            'created_at' => now()->subHours(2),
        ]);

        // Ticket 4: Feature Request (Open)
        $ticket4 = Ticket::create([
            'title' => 'Feature Request: Dark mode for mobile app',
            'description' => 'Hi team, I love using your platform but I noticed that the mobile app doesn\'t have a dark mode option. I do a lot of work in the evenings and the bright white interface can be quite straining on the eyes. Would it be possible to add a dark mode toggle to the mobile app? This would be a great addition for users like me who work in low-light environments.',
            'status' => 'open',
            'created_by' => $user1->id,
            'created_at' => now()->subDays(5),
        ]);

        Reply::create([
            'ticket_id' => $ticket4->id,
            'user_id' => $agent2->id,
            'content' => 'Hi John, thank you for this excellent suggestion! Dark mode is indeed a highly requested feature. I\'ve forwarded your request to our product development team. While I can\'t provide a specific timeline, I can tell you that dark mode is on our roadmap for this year. I\'ll keep you updated on any progress.',
            'created_at' => now()->subDays(4),
        ]);

        // Ticket 5: Account Access Issue (New)
        $ticket5 = Ticket::create([
            'title' => 'Locked out of account after multiple login attempts',
            'description' => 'I accidentally typed my password wrong several times and now my account is locked. I can see a message saying "Account temporarily locked due to multiple failed login attempts". How long do I need to wait, or is there a way to unlock it sooner? My email is robert@example.com.',
            'status' => 'open',
            'created_by' => $user3->id,
            'created_at' => now()->subHours(2),
        ]);

        // Create some additional random tickets with replies
        $randomTickets = Ticket::factory(15)->create();
        
        // Add random replies to some of the random tickets
        $this->addRandomRepliesToTickets($randomTickets, [$agent1, $agent2]);
    }

    private function addRandomRepliesToTickets($tickets, $agents)
    {
        $supportResponses = [
            "Thank you for contacting support. I'm looking into this issue for you.",
            "I've escalated this to our technical team. You should hear back within 24 hours.",
            "Could you please provide more details about when this issue started?",
            "I've found a solution for your issue. Please try the following steps:",
            "This is a known issue that we're currently working on. I'll keep you updated.",
            "I've updated your account settings. Please try again and let me know if it works.",
            "Thank you for the additional information. This helps us understand the problem better.",
            "I've processed your request. The changes should take effect within the next hour.",
            "Could you please clear your browser cache and try again?",
            "I've forwarded this to our billing department for review.",
        ];

        $userResponses = [
            "Thank you for the quick response!",
            "That worked perfectly, thank you so much!",
            "I'm still having the same issue. Here are more details:",
            "The problem started yesterday around 3 PM.",
            "I tried those steps but I'm still getting the error.",
            "Great! Everything is working now.",
            "I appreciate your help with this.",
            "Could you also help me with a related question?",
            "Thanks for looking into this. I'll wait for the update.",
            "The issue is resolved now. Thank you!",
        ];

        foreach ($tickets as $ticket) {
            // 70% chance of having replies
            if (rand(1, 100) <= 70) {
                $replyCount = rand(1, 4); // 1 to 4 replies per ticket
                
                for ($i = 0; $i < $replyCount; $i++) {
                    $isAgentReply = ($i % 2 === 0); // Alternate between agent and user
                    
                    if ($isAgentReply) {
                        $userId = $agents[array_rand($agents)]->id;
                        $content = $supportResponses[array_rand($supportResponses)];
                    } else {
                        $userId = $ticket->created_by;
                        $content = $userResponses[array_rand($userResponses)];
                    }
                    
                    Reply::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $userId,
                        'content' => $content,
                        'created_at' => $ticket->created_at->addHours(rand(1, 48)),
                    ]);
                }
                
                // Update ticket status based on replies
                if ($replyCount >= 3 && rand(1, 100) <= 40) {
                    $ticket->update(['status' => 'closed']);
                } elseif ($replyCount >= 2 && rand(1, 100) <= 60) {
                    $ticket->update(['status' => 'in_progress']);
                }
            }
        }
    }
}
