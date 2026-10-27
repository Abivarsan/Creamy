using System.Windows;
using Creamy.WPF.ViewModels;

namespace Creamy.WPF.Views
{
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            DataContext = new MainViewModel();
        }
    }
}
