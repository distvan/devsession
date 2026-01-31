# DevSession

A lightweight CLI tool to track focused development sessions with git aware summaries.

DevSession helps developers answer a simple but important question:
What did I actually work on today?

## Features

- Start and end focused development sessions
- Automatically detects:
    - git repository
    - current branch
    - commits made during the session
    - files changed
- view
    - active session status
    - today's sessions
    - historical session log

- Local JSON storage (no setup, no cloud)
- Written in pure PHP (without framework)

## Requirements

- PHP 8.1 or higher
- Composer
- Git (optional, for repository tracking)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/devsession.git
cd devsession
```

2. Install dependencies:
```bash
composer install
```

3. Make the script executable (Unix/Linux/macOS):
```bash
chmod +x devsession.php
```

4. (Optional) Add to your PATH for global access:
```bash
# Add to ~/.bashrc or ~/.zshrc
export PATH="$PATH:/path/to/devsession"
```

## Usage

### Start a Session
Begin tracking a new development session with a descriptive title:
```bash
php devsession.php start "Implementing user authentication"
```

### Check Status
View the currently active session:
```bash
php devsession.php status
```

### End a Session
Complete the active session and save all tracked information:
```bash
php devsession.php end
```

### View Today's Sessions
See all sessions from today:
```bash
php devsession.php today
```

### View Session History
Browse all recorded sessions:
```bash
php devsession.php log
```

### Get Help
Display available commands:
```bash
php devsession.php help
```

## How It Works

1. **Start**: Creates a new session with title, timestamp, and git context (if available)
2. **Track**: Monitors your working directory for changes during the session
3. **End**: Captures commits made, files changed, and session duration
4. **Store**: Saves everything to local JSON files in the `storage/` directory

## Storage

All session data is stored locally in JSON format under the `storage/` directory:
- No external dependencies
- No cloud sync
- Full privacy and control
- Easy to backup or version control

## Example Workflow

```bash
# Morning: Start working on a feature
$ php devsession.php start "Add password reset feature"
✓ Session started
Title: Add password reset feature
Branch: feature/password-reset

# ... code, commit, code, commit ...

# Afternoon: Check what you've done
$ php devsession.php status
Active Session: Add password reset feature
Started: 09:15 AM
Duration: 3h 42m
Branch: feature/password-reset

# End of day: Close the session
$ php devsession.php end
✓ Session ended

# Review your day
$ php devsession.php today
```

## GitHub Copilot CLI

This project was built with help from Github Copilot CLI, used to:
- design the CLI structure
- generate PHP command dispatching
- integrate git commands
- format terminal output
- iterate on architecture decisions quickly

Copilot acted as a pair programmer in the terminal, not a code generator.

## License

MIT License - feel free to use and modify as needed.

## Author

**Istvan Dobrentei**
- Email: info@dobrenteiistvan.hu
- Website: https://www.en.dobrenteiistvan.hu